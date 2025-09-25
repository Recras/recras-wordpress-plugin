<?php
namespace Recras;

class PluginTest extends WordPressUnitTestCase
{
    function testInvalidSubdomain(): void
    {
        $plugin = new Settings();
        $result = $plugin->sanitizeDomain('foo@bar.recras.nl');
        $this->assertFalse($result, 'Subdomain with invalid characters should be invalid');
    }
    function testInvalidExtension(): void
    {
        $plugin = new Settings();
        $result = $plugin->sanitizeDomain('demo.recras.tk');
        $this->assertFalse($result, 'Domain with invalid extension should be invalid');
    }

    function testValidSubdomainNL(): void
    {
        $plugin = new Settings();
        $result = $plugin->sanitizeDomain('demo.recras.nl');
        $this->assertEquals('demo', $result, 'Valid domain should be valid');
    }

    function testValidSubdomainCom(): void
    {
        $plugin = new Settings();
        $result = $plugin->sanitizeDomain('demo.recras.com');
        $this->assertEquals('demo', $result, 'Valid domain should be valid');
    }
}
