<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit885f4372c74fa1c08e0d9d1c46f60180
{
    public static $prefixLengthsPsr4 = array (
        'c' => 
        array (
            'cebe\\markdown\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'cebe\\markdown\\' => 
        array (
            0 => __DIR__ . '/..' . '/cebe/markdown',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'PEG' => 
            array (
                0 => __DIR__ . '/..' . '/phppeg/phppeg/code',
            ),
            'PEAR' => 
            array (
                0 => __DIR__ . '/..' . '/pear/pear_exception',
            ),
        ),
        'H' => 
        array (
            'HatenaSyntax' => 
            array (
                0 => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib',
            ),
            'HTTP_Request2' => 
            array (
                0 => __DIR__ . '/..' . '/pear/http_request2',
            ),
        ),
    );

    public static $classMap = array (
        'HTTP_Request2' => __DIR__ . '/..' . '/pear/http_request2/HTTP/Request2.php',
        'HTTP_Request2Test' => __DIR__ . '/..' . '/pear/http_request2/tests/Request2Test.php',
        'HTTP_Request2_Adapter' => __DIR__ . '/..' . '/pear/http_request2/HTTP/Request2/Adapter.php',
        'HTTP_Request2_Adapter_CommonNetworkTest' => __DIR__ . '/..' . '/pear/http_request2/tests/Request2/Adapter/CommonNetworkTest.php',
        'HTTP_Request2_Adapter_Curl' => __DIR__ . '/..' . '/pear/http_request2/HTTP/Request2/Adapter/Curl.php',
        'HTTP_Request2_Adapter_CurlTest' => __DIR__ . '/..' . '/pear/http_request2/tests/Request2/Adapter/CurlTest.php',
        'HTTP_Request2_Adapter_Mock' => __DIR__ . '/..' . '/pear/http_request2/HTTP/Request2/Adapter/Mock.php',
        'HTTP_Request2_Adapter_MockTest' => __DIR__ . '/..' . '/pear/http_request2/tests/Request2/Adapter/MockTest.php',
        'HTTP_Request2_Adapter_Skip_CurlTest' => __DIR__ . '/..' . '/pear/http_request2/tests/Request2/Adapter/SkippedTests.php',
        'HTTP_Request2_Adapter_Skip_SocketProxyTest' => __DIR__ . '/..' . '/pear/http_request2/tests/Request2/Adapter/SkippedTests.php',
        'HTTP_Request2_Adapter_Skip_SocketTest' => __DIR__ . '/..' . '/pear/http_request2/tests/Request2/Adapter/SkippedTests.php',
        'HTTP_Request2_Adapter_Socket' => __DIR__ . '/..' . '/pear/http_request2/HTTP/Request2/Adapter/Socket.php',
        'HTTP_Request2_Adapter_SocketProxyTest' => __DIR__ . '/..' . '/pear/http_request2/tests/Request2/Adapter/SocketProxyTest.php',
        'HTTP_Request2_Adapter_SocketTest' => __DIR__ . '/..' . '/pear/http_request2/tests/Request2/Adapter/SocketTest.php',
        'HTTP_Request2_AllTests' => __DIR__ . '/..' . '/pear/http_request2/tests/AllTests.php',
        'HTTP_Request2_ConnectionException' => __DIR__ . '/..' . '/pear/http_request2/HTTP/Request2/Exception.php',
        'HTTP_Request2_CookieJar' => __DIR__ . '/..' . '/pear/http_request2/HTTP/Request2/CookieJar.php',
        'HTTP_Request2_CookieJarTest' => __DIR__ . '/..' . '/pear/http_request2/tests/Request2/CookieJarTest.php',
        'HTTP_Request2_Exception' => __DIR__ . '/..' . '/pear/http_request2/HTTP/Request2/Exception.php',
        'HTTP_Request2_LogicException' => __DIR__ . '/..' . '/pear/http_request2/HTTP/Request2/Exception.php',
        'HTTP_Request2_MessageException' => __DIR__ . '/..' . '/pear/http_request2/HTTP/Request2/Exception.php',
        'HTTP_Request2_MockObserver' => __DIR__ . '/..' . '/pear/http_request2/tests/ObserverTest.php',
        'HTTP_Request2_MultipartBody' => __DIR__ . '/..' . '/pear/http_request2/HTTP/Request2/MultipartBody.php',
        'HTTP_Request2_MultipartBodyTest' => __DIR__ . '/..' . '/pear/http_request2/tests/Request2/MultipartBodyTest.php',
        'HTTP_Request2_NotImplementedException' => __DIR__ . '/..' . '/pear/http_request2/HTTP/Request2/Exception.php',
        'HTTP_Request2_ObserverTest' => __DIR__ . '/..' . '/pear/http_request2/tests/ObserverTest.php',
        'HTTP_Request2_Observer_Log' => __DIR__ . '/..' . '/pear/http_request2/HTTP/Request2/Observer/Log.php',
        'HTTP_Request2_Observer_UncompressingDownload' => __DIR__ . '/..' . '/pear/http_request2/HTTP/Request2/Observer/UncompressingDownload.php',
        'HTTP_Request2_Response' => __DIR__ . '/..' . '/pear/http_request2/HTTP/Request2/Response.php',
        'HTTP_Request2_ResponseTest' => __DIR__ . '/..' . '/pear/http_request2/tests/Request2/ResponseTest.php',
        'HTTP_Request2_SOCKS5' => __DIR__ . '/..' . '/pear/http_request2/HTTP/Request2/SOCKS5.php',
        'HTTP_Request2_SocketWrapper' => __DIR__ . '/..' . '/pear/http_request2/HTTP/Request2/SocketWrapper.php',
        'HatenaSyntax' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax.php',
        'HatenaSyntax_Block' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/Block.php',
        'HatenaSyntax_CommentRemover' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/CommentRemover.php',
        'HatenaSyntax_DefinitionList' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/DefinitionList.php',
        'HatenaSyntax_Header' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/Header.php',
        'HatenaSyntax_InlineTag' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/InlineTag.php',
        'HatenaSyntax_LineElement' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/LineElement.php',
        'HatenaSyntax_List' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/List.php',
        'HatenaSyntax_Locator' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/Locator.php',
        'HatenaSyntax_NoParagraph' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/NoParagraph.php',
        'HatenaSyntax_Node' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/Node.php',
        'HatenaSyntax_NodeCreater' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/NodeCreater.php',
        'HatenaSyntax_Paragraph' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/Paragraph.php',
        'HatenaSyntax_Pre' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/Pre.php',
        'HatenaSyntax_Quote' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/Quote.php',
        'HatenaSyntax_Regex' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/Regex.php',
        'HatenaSyntax_Renderer' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/Renderer.php',
        'HatenaSyntax_SuperPre' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/SuperPre.php',
        'HatenaSyntax_TOCRenderer' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/TOCRenderer.php',
        'HatenaSyntax_Table' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/Table.php',
        'HatenaSyntax_Tree' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/Tree.php',
        'HatenaSyntax_TreeRenderer' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/TreeRenderer.php',
        'HatenaSyntax_Tree_INode' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/Tree/INode.php',
        'HatenaSyntax_Tree_Leaf' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/Tree/Leaf.php',
        'HatenaSyntax_Tree_Node' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/Tree/Node.php',
        'HatenaSyntax_Tree_Root' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/Tree/Root.php',
        'HatenaSyntax_Util' => __DIR__ . '/..' . '/hatenasyntax/hatenasyntax/lib/HatenaSyntax/Util.php',
        'Net_URL2' => __DIR__ . '/..' . '/pear/net_url2/Net/URL2.php',
        'PEAR_Exception' => __DIR__ . '/..' . '/pear/pear_exception/PEAR/Exception.php',
        'PEAR_ExceptionTest' => __DIR__ . '/..' . '/pear/pear_exception/tests/PEAR/ExceptionTest.php',
        'PEG' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG.php',
        'PEG_Action' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/Action.php',
        'PEG_And' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/And.php',
        'PEG_Anything' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/Anything.php',
        'PEG_ArrayContext' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/ArrayContext.php',
        'PEG_Cache' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/Cache.php',
        'PEG_CallbackAction' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/CallbackAction.php',
        'PEG_Char' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/Char.php',
        'PEG_Choice' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/Choice.php',
        'PEG_Curry' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/Curry.php',
        'PEG_Delay' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/Delay.php',
        'PEG_EOS' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/EOS.php',
        'PEG_ErrorReporter' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/ErrorReporter.php',
        'PEG_Failure' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/Failure.php',
        'PEG_IContext' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/IContext.php',
        'PEG_IParser' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/IParser.php',
        'PEG_InstantParser' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/InstantParser.php',
        'PEG_Lookahead' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/Lookahead.php',
        'PEG_Many' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/Many.php',
        'PEG_Memoize' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/Memoize.php',
        'PEG_Not' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/Not.php',
        'PEG_Optional' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/Optional.php',
        'PEG_Ref' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/Ref.php',
        'PEG_Sequence' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/Sequence.php',
        'PEG_StringContext' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/StringContext.php',
        'PEG_Token' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/Token.php',
        'PEG_Util' => __DIR__ . '/..' . '/phppeg/phppeg/code/PEG/Util.php',
        'cebe\\markdown\\GithubMarkdown' => __DIR__ . '/..' . '/cebe/markdown/GithubMarkdown.php',
        'cebe\\markdown\\Markdown' => __DIR__ . '/..' . '/cebe/markdown/Markdown.php',
        'cebe\\markdown\\MarkdownExtra' => __DIR__ . '/..' . '/cebe/markdown/MarkdownExtra.php',
        'cebe\\markdown\\Parser' => __DIR__ . '/..' . '/cebe/markdown/Parser.php',
        'cebe\\markdown\\block\\CodeTrait' => __DIR__ . '/..' . '/cebe/markdown/block/CodeTrait.php',
        'cebe\\markdown\\block\\FencedCodeTrait' => __DIR__ . '/..' . '/cebe/markdown/block/FencedCodeTrait.php',
        'cebe\\markdown\\block\\HeadlineTrait' => __DIR__ . '/..' . '/cebe/markdown/block/HeadlineTrait.php',
        'cebe\\markdown\\block\\HtmlTrait' => __DIR__ . '/..' . '/cebe/markdown/block/HtmlTrait.php',
        'cebe\\markdown\\block\\ListTrait' => __DIR__ . '/..' . '/cebe/markdown/block/ListTrait.php',
        'cebe\\markdown\\block\\QuoteTrait' => __DIR__ . '/..' . '/cebe/markdown/block/QuoteTrait.php',
        'cebe\\markdown\\block\\RuleTrait' => __DIR__ . '/..' . '/cebe/markdown/block/RuleTrait.php',
        'cebe\\markdown\\block\\TableTrait' => __DIR__ . '/..' . '/cebe/markdown/block/TableTrait.php',
        'cebe\\markdown\\inline\\CodeTrait' => __DIR__ . '/..' . '/cebe/markdown/inline/CodeTrait.php',
        'cebe\\markdown\\inline\\EmphStrongTrait' => __DIR__ . '/..' . '/cebe/markdown/inline/EmphStrongTrait.php',
        'cebe\\markdown\\inline\\LinkTrait' => __DIR__ . '/..' . '/cebe/markdown/inline/LinkTrait.php',
        'cebe\\markdown\\inline\\StrikeoutTrait' => __DIR__ . '/..' . '/cebe/markdown/inline/StrikeoutTrait.php',
        'cebe\\markdown\\inline\\UrlLinkTrait' => __DIR__ . '/..' . '/cebe/markdown/inline/UrlLinkTrait.php',
        'cebe\\markdown\\tests\\BaseMarkdownTest' => __DIR__ . '/..' . '/cebe/markdown/tests/BaseMarkdownTest.php',
        'cebe\\markdown\\tests\\GithubMarkdownTest' => __DIR__ . '/..' . '/cebe/markdown/tests/GithubMarkdownTest.php',
        'cebe\\markdown\\tests\\MarkdownExtraTest' => __DIR__ . '/..' . '/cebe/markdown/tests/MarkdownExtraTest.php',
        'cebe\\markdown\\tests\\MarkdownOLStartNumTest' => __DIR__ . '/..' . '/cebe/markdown/tests/MarkdownOLStartNumTest.php',
        'cebe\\markdown\\tests\\MarkdownTest' => __DIR__ . '/..' . '/cebe/markdown/tests/MarkdownTest.php',
        'cebe\\markdown\\tests\\ParserTest' => __DIR__ . '/..' . '/cebe/markdown/tests/ParserTest.php',
        'cebe\\markdown\\tests\\TestParser' => __DIR__ . '/..' . '/cebe/markdown/tests/ParserTest.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit885f4372c74fa1c08e0d9d1c46f60180::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit885f4372c74fa1c08e0d9d1c46f60180::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit885f4372c74fa1c08e0d9d1c46f60180::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit885f4372c74fa1c08e0d9d1c46f60180::$classMap;

        }, null, ClassLoader::class);
    }
}
