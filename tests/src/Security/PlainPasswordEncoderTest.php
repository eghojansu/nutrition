<?php

namespace Nutrition\Test\Security;

use MyTestCase;
use Nutrition\Security\PlainPasswordEncoder;

class PlainPasswordEncoderTest extends MyTestCase
{
    private $encoder;
    private $password = 'password';
    private $hash = 'password';

    protected function setUp()
    {
        $this->encoder = new PlainPasswordEncoder();
    }

    public function testEncodePassword()
    {
        $this->assertEquals($this->hash, $this->encoder->encodePassword($this->password));
    }

    public function testVerifyPassword()
    {
        $this->assertTrue($this->encoder->verifyPassword($this->password, $this->hash));
        $this->assertFalse($this->encoder->verifyPassword('invalid-password', $this->hash));
    }
}
