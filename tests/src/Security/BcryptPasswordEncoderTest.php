<?php

namespace Nutrition\Test\Security;

use MyTestCase;
use Nutrition\Security\BcryptPasswordEncoder;

class BcryptPasswordEncoderTest extends MyTestCase
{
    private $encoder;
    private $password = 'password';
    private $hash = '$2y$10$C.mlNCSCJClvGgwF6wdsT.7b7aG9ScVBOXzvM4Zl/LVHnpy/COrlq';

    protected function setUp()
    {
        $this->encoder = new BcryptPasswordEncoder();
    }

    public function testEncodePassword()
    {
        $this->assertNotEquals($this->hash, $this->encoder->encodePassword($this->password));
    }

    public function testVerifyPassword()
    {
        $this->assertTrue($this->encoder->verifyPassword($this->password, $this->hash));
        $this->assertFalse($this->encoder->verifyPassword('invalid-password', $this->hash));
    }
}
