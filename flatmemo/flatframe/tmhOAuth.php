<?php
class tmhOAuth
{
    const VERSION = '0.8.2';
    public $response = array();
    public function __construct($config = array())
    {
        $this->buffer = null;
        $this->reconfigure($config);
        $this->reset_request_settings();
        $this->set_user_agent();
    }
    public function reconfigure($config = array())
    {
        $this->config = array_merge(array('user_agent' => '', 'host' => 'api.twitter.com', 'consumer_key' => '', 'consumer_secret' => '', 'token' => '', 'secret' => '', 'bearer' => '', 'oauth_version' => '1.0', 'oauth_signature_method' => 'HMAC-SHA1', 'curl_connecttimeout' => 30, 'curl_timeout' => 10, 'curl_ssl_verifyhost' => 2, 'curl_ssl_verifypeer' => true, 'use_ssl' => true, 'curl_cainfo' => __DIR__ . DIRECTORY_SEPARATOR . 'cacert.pem', 'curl_capath' => __DIR__, 'curl_followlocation' => false, 'curl_proxy' => false, 'curl_proxyuserpwd' => false, 'curl_encoding' => '', 'is_streaming' => false, 'streaming_eol' => "\r\n", 'streaming_metrics_interval' => 10, 'as_header' => true, 'force_nonce' => false, 'force_timestamp' => false,), $config);
    }
    private function reset_request_settings($options = array())
    {
        $this->request_settings = array('params' => array(), 'headers' => array(), 'with_user' => true, 'multipart' => false,);
        if (!empty($options)) {
            $this->request_settings = array_merge($this->request_settings, $options);
        }
    }
    private function set_user_agent()
    {
        if (!empty($this->config['user_agent'])) {
            return;
        }
        $ssl = ($this->config['curl_ssl_verifyhost'] && $this->config['curl_ssl_verifypeer'] && $this->config['use_ssl']) ? '+' : '-';
        $ua = 'tmhOAuth ' . self::VERSION . $ssl . 'SSL - //github.com/themattharris/tmhOAuth';
        $this->config['user_agent'] = $ua;
    }
    private function nonce($length = 12, $include_time = true)
    {
        if ($this->config['force_nonce'] === false) {
            $prefix = $include_time ? microtime() : '';
            return md5(substr($prefix . uniqid(), 0, $length));
        } else {
            return $this->config['force_nonce'];
        }
    }
    private function timestamp()
    {
        if ($this->config['force_timestamp'] === false) {
            $time = time();
        } else {
            $time = $this->config['force_timestamp'];
        }
        return (string)$time;
    }
    private function safe_encode($data)
    {
        if (is_array($data)) {
            return array_map(array($this, 'safe_encode'), $data);
        } elseif (is_scalar($data)) {
            return str_ireplace(array('+', '%7E'), array(' ', '~'), rawurlencode($data));
        } else {
            return '';
        }
    }
    private function safe_decode($data)
    {
        if (is_array($data)) {
            return array_map(array($this, 'safe_decode'), $data);
        } elseif (is_scalar($data)) {
            return rawurldecode($data);
        } else {
            return '';
        }
    }
    private function prepare_oauth1_params()
    {
        $defaults = array('oauth_nonce' => $this->nonce(), 'oauth_timestamp' => $this->timestamp(), 'oauth_version' => $this->config['oauth_version'], 'oauth_consumer_key' => $this->config['consumer_key'], 'oauth_signature_method' => $this->config['oauth_signature_method'],);
        if ($oauth_token = $this->token()) {
            $defaults['oauth_token'] = $oauth_token;
        }
        $this->request_settings['oauth1_params'] = array();
        foreach ($defaults as $k => $v) {
            $this->request_settings['oauth1_params'][$this->safe_encode($k) ] = $this->safe_encode($v);
        }
    }
    private function token()
    {
        if ($this->request_settings['with_user']) {
            if (isset($this->config['token']) && !empty($this->config['token'])) {
                return $this->config['token'];
            } elseif (isset($this->config['user_token'])) {
                return $this->config['user_token'];
            }
        }
        return '';
    }
    private function secret()
    {
        if ($this->request_settings['with_user']) {
            if (isset($this->config['secret']) && !empty($this->config['secret'])) {
                return $this->config['secret'];
            } elseif (isset($this->config['user_secret'])) {
                return $this->config['user_secret'];
            }
        }
        return '';
    }
    public function extract_params($body)
    {
        $kvs = explode('&', $body);
        $decoded = array();
        foreach ($kvs as $kv) {
            $kv = explode('=', $kv, 2);
            $kv[0] = $this->safe_decode($kv[0]);
            $kv[1] = $this->safe_decode($kv[1]);
            $decoded[$kv[0]] = $kv[1];
        }
        return $decoded;
    }
    private function prepare_method()
    {
        $this->request_settings['method'] = strtoupper($this->request_settings['method']);
    }
    private function prepare_url()
    {
        $parts = parse_url($this->request_settings['url']);
        $port = isset($parts['port']) ? $parts['port'] : false;
        $scheme = $parts['scheme'];
        $host = $parts['host'];
        $path = isset($parts['path']) ? $parts['path'] : false;
        $port or $port = ($scheme == 'https') ? '443' : '80';
        if (($scheme == 'https' && $port != '443') || ($scheme == 'http' && $port != '80')) {
            $host = "$host:$port";
        }
        $this->request_settings['url'] = strtolower("$scheme://$host");
        $this->request_settings['url'].= $path;
    }
    private function prepare_params()
    {
        $doing_oauth1 = false;
        $this->request_settings['prepared_params'] = array();
        $prepared = & $this->request_settings['prepared_params'];
        $prepared_pairs = array();
        $prepared_pairs_with_oauth = array();
        if (isset($this->request_settings['oauth1_params'])) {
            $oauth1 = & $this->request_settings['oauth1_params'];
            $doing_oauth1 = true;
            $params = array_merge($oauth1, $this->request_settings['params']);
            unset($params['oauth_signature']);
            $oauth1 = array();
        } else {
            $params = $this->request_settings['params'];
        }
        uksort($params, 'strcmp');
        foreach ($params as $k => $v) {
            $k = $this->request_settings['multipart'] ? $k : $this->safe_encode($k);
            if (is_array($v)) {
                $v = implode(',', $v);
            }
            $v = $this->request_settings['multipart'] ? $v : $this->safe_encode($v);
            if ($doing_oauth1) {
                if ((strpos($k, 'oauth') === 0) || !$this->request_settings['multipart']) {
                    $prepared_pairs_with_oauth[] = "{$k}={$v}";
                }
                if (strpos($k, 'oauth') === 0) {
                    $oauth1[$k] = $v;
                    continue;
                }
            }
            $prepared[$k] = $v;
            $prepared_pairs[] = "{$k}={$v}";
        }
        if ($doing_oauth1) {
            $this->request_settings['basestring_params'] = implode('&', $prepared_pairs_with_oauth);
        }
        if (!empty($prepared_pairs)) {
            $content = implode('&', $prepared_pairs);
            switch ($this->request_settings['method']) {
                case 'POST':
                    $this->request_settings['postfields'] = $this->request_settings['multipart'] ? $prepared : $content;
                break;
                default:
                    $this->request_settings['querystring'] = $content;
                break;
            }
        }
    }
    private function prepare_signing_key()
    {
        $left = $this->safe_encode($this->config['consumer_secret']);
        $right = $this->safe_encode($this->secret());
        $this->request_settings['signing_key'] = $left . '&' . $right;
    }
    private function prepare_base_string()
    {
        $url = $this->request_settings['url'];
        if (!empty($this->request_settings['headers']['Host'])) {
            $url = str_ireplace($this->config['host'], $this->request_settings['headers']['Host'], $url);
        }
        $base = array($this->request_settings['method'], $url, $this->request_settings['basestring_params']);
        $this->request_settings['basestring'] = implode('&', $this->safe_encode($base));
    }
    private function prepare_oauth_signature()
    {
        $this->request_settings['oauth1_params']['oauth_signature'] = $this->safe_encode(base64_encode(hash_hmac('sha1', $this->request_settings['basestring'], $this->request_settings['signing_key'], true)));
    }
    private function prepare_auth_header()
    {
        if (!$this->config['as_header']) {
            return;
        }
        if (isset($this->request_settings['oauth1_params'])) {
            uksort($this->request_settings['oauth1_params'], 'strcmp');
            $encoded_quoted_pairs = array();
            foreach ($this->request_settings['oauth1_params'] as $k => $v) {
                $encoded_quoted_pairs[] = "{$k}=\"{$v}\"";
            }
            $header = 'OAuth ' . implode(', ', $encoded_quoted_pairs);
        } elseif (!empty($this->config['bearer'])) {
            $header = 'Bearer ' . $this->config['bearer'];
        }
        if (isset($header)) {
            $this->request_settings['headers']['Authorization'] = $header;
        }
    }
    public function bearer_token_credentials()
    {
        $credentials = implode(':', array($this->safe_encode($this->config['consumer_key']), $this->safe_encode($this->config['consumer_secret'])));
        return base64_encode($credentials);
    }
    public function request($method, $url, $params = array(), $useauth = true, $multipart = false, $headers = array())
    {
        $options = array('method' => $method, 'url' => $url, 'params' => $params, 'with_user' => true, 'multipart' => $multipart, 'headers' => $headers);
        $options = array_merge($this->default_options(), $options);
        if ($useauth) {
            return $this->user_request($options);
        } else {
            return $this->unauthenticated_request($options);
        }
    }
    public function apponly_request($options = array())
    {
        $options = array_merge($this->default_options(), $options, array('with_user' => false,));
        $this->reset_request_settings($options);
        if ($options['without_bearer']) {
            return $this->oauth1_request();
        } else {
            $this->prepare_method();
            $this->prepare_url();
            $this->prepare_params();
            $this->prepare_auth_header();
            return $this->curlit();
        }
    }
    public function user_request($options = array())
    {
        $options = array_merge($this->default_options(), $options, array('with_user' => true,));
        $this->reset_request_settings($options);
        return $this->oauth1_request();
    }
    public function unauthenticated_request($options = array())
    {
        $options = array_merge($this->default_options(), $options, array('with_user' => false,));
        $this->reset_request_settings($options);
        $this->prepare_method();
        $this->prepare_url();
        $this->prepare_params();
        return $this->curlit();
    }
    private function oauth1_request()
    {
        $this->prepare_oauth1_params();
        $this->prepare_method();
        $this->prepare_url();
        $this->prepare_params();
        $this->prepare_base_string();
        $this->prepare_signing_key();
        $this->prepare_oauth_signature();
        $this->prepare_auth_header();
        return $this->curlit();
    }
    private function default_options()
    {
        return array('method' => 'GET', 'params' => array(), 'with_user' => true, 'multipart' => false, 'headers' => array(), 'without_bearer' => false,);
    }
    public function streaming_request($method, $url, $params = array(), $callback = '')
    {
        if (!empty($callback)) {
            if (!is_callable($callback)) {
                return false;
            }
            $this->config['streaming_callback'] = $callback;
        }
        $this->metrics['start'] = time();
        $this->metrics['interval_start'] = $this->metrics['start'];
        $this->metrics['messages'] = 0;
        $this->metrics['last_messages'] = 0;
        $this->metrics['bytes'] = 0;
        $this->metrics['last_bytes'] = 0;
        $this->config['is_streaming'] = true;
        $this->request($method, $url, $params);
    }
    private function update_metrics()
    {
        $now = time();
        if (($this->metrics['interval_start'] + $this->config['streaming_metrics_interval']) > $now) {
            return null;
        }
        $this->metrics['mps'] = round(($this->metrics['messages'] - $this->metrics['last_messages']) / $this->config['streaming_metrics_interval'], 2);
        $this->metrics['bps'] = round(($this->metrics['bytes'] - $this->metrics['last_bytes']) / $this->config['streaming_metrics_interval'], 2);
        $this->metrics['last_bytes'] = $this->metrics['bytes'];
        $this->metrics['last_messages'] = $this->metrics['messages'];
        $this->metrics['interval_start'] = $now;
        return $this->metrics;
    }
    public function url($request, $extension = 'json')
    {
        $request = preg_replace('$([^:])//+$', '$1/', $request);
        if (stripos($request, 'http') === 0 || stripos($request, '//') === 0) {
            return $request;
        }
        $extension = strlen($extension) > 0 ? ".$extension" : '';
        $proto = $this->config['use_ssl'] ? 'https:/' : 'http:/';
        $request = ltrim($request, '/');
        $pos = strlen($request) - strlen($extension);
        if (substr($request, $pos) === $extension) {
            $request = substr_replace($request, '', $pos);
        }
        return implode('/', array($proto, $this->config['host'], $request . $extension));
    }
    public function transformText($text, $mode = 'encode')
    {
        return $this->{"safe_$mode"}($text);
    }
    private function curlHeader($ch, $header)
    {
        $this->response['raw'].= $header;
        list($key, $value) = array_pad(explode(':', $header, 2), 2, null);
        $key = trim($key);
        $value = trim($value);
        if (!isset($this->response['headers'][$key])) {
            $this->response['headers'][$key] = $value;
        } else {
            if (!is_array($this->response['headers'][$key])) {
                $this->response['headers'][$key] = array($this->response['headers'][$key]);
            }
            $this->response['headers'][$key][] = $value;
        }
        return strlen($header);
    }
    private function curlWrite($ch, $data)
    {
        $l = strlen($data);
        if (strpos($data, $this->config['streaming_eol']) === false) {
            $this->buffer.= $data;
            return $l;
        }
        $buffered = explode($this->config['streaming_eol'], $data);
        $content = $this->buffer . $buffered[0];
        $this->metrics['messages']++;
        $this->metrics['bytes']+= strlen($content);
        if (!is_callable($this->config['streaming_callback'])) {
            return 0;
        }
        $metrics = $this->update_metrics();
        $stop = call_user_func($this->config['streaming_callback'], $content, strlen($content), $metrics);
        $this->buffer = $buffered[1];
        if ($stop) {
            return 0;
        }
        return $l;
    }
    private function curlit()
    {
        $this->response = array('raw' => '');
        $c = curl_init();
        switch ($this->request_settings['method']) {
            case 'GET':
                if (isset($this->request_settings['querystring'])) {
                    $this->request_settings['url'] = $this->request_settings['url'] . '?' . $this->request_settings['querystring'];
                }
                break;
            case 'POST':
                curl_setopt($c, CURLOPT_POST, true);
                if (isset($this->request_settings['postfields'])) {
                    $postfields = $this->request_settings['postfields'];
                } else {
                    $postfields = array();
                }
                curl_setopt($c, CURLOPT_POSTFIELDS, $postfields);
                break;
            default:
                if (isset($this->request_settings['postfields'])) {
                    curl_setopt($c, CURLOPT_CUSTOMREQUEST, $this->request_settings['postfields']);
                }
            }
        curl_setopt_array($c, array(CURLOPT_USERAGENT => $this->config['user_agent'], CURLOPT_CONNECTTIMEOUT => $this->config['curl_connecttimeout'], CURLOPT_TIMEOUT => $this->config['curl_timeout'], CURLOPT_RETURNTRANSFER => true, CURLOPT_SSL_VERIFYPEER => $this->config['curl_ssl_verifypeer'], CURLOPT_SSL_VERIFYHOST => $this->config['curl_ssl_verifyhost'], CURLOPT_FOLLOWLOCATION => $this->config['curl_followlocation'], CURLOPT_PROXY => $this->config['curl_proxy'], CURLOPT_ENCODING => $this->config['curl_encoding'], CURLOPT_URL => $this->request_settings['url'], CURLOPT_HEADERFUNCTION => array($this, 'curlHeader'), CURLOPT_HEADER => false, CURLINFO_HEADER_OUT => true,));
        if ($this->config['curl_cainfo'] !== false) {
            curl_setopt($c, CURLOPT_CAINFO, $this->config['curl_cainfo']);
        }
        if ($this->config['curl_capath'] !== false) {
            curl_setopt($c, CURLOPT_CAPATH, $this->config['curl_capath']);
        }
        if ($this->config['curl_proxyuserpwd'] !== false) {
            curl_setopt($c, CURLOPT_PROXYUSERPWD, $this->config['curl_proxyuserpwd']);
        }
        if ($this->config['is_streaming']) {
            $this->response['content-length'] = 0;
            curl_setopt($c, CURLOPT_TIMEOUT, 0);
            curl_setopt($c, CURLOPT_WRITEFUNCTION, array($this, 'curlWrite'));
        }
        if (!empty($this->request_settings['headers'])) {
            foreach ($this->request_settings['headers'] as $k => $v) {
                $headers[] = trim($k . ': ' . $v);
            }
            curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
        }
        if (isset($this->config['block']) && (true === $this->config['block'])) {
            return 0;
        }
        $response = curl_exec($c);
        $code = curl_getinfo($c, CURLINFO_HTTP_CODE);
        $info = curl_getinfo($c);
        $error = curl_error($c);
        $errno = curl_errno($c);
        curl_close($c);
        $this->response['code'] = $code;
        $this->response['response'] = $response;
        $this->response['info'] = $info;
        $this->response['error'] = $error;
        $this->response['errno'] = $errno;
        if (!isset($this->response['raw'])) {
            $this->response['raw'] = '';
        }
        $this->response['raw'].= $response;
        return $code;
    }
}
