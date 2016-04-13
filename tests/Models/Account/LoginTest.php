<?php
namespace App\Models\Account;

use App\Context\ErrorKeys;
use App\Context\ValidationException;
use App\Tests\EntityTestSupport;

class LoginTest extends EntityTestSupport
{
    protected function initialize()
    {
        $this->fixtures->login('test')->save();
    }

    public function testRegister()
    {
        $this->assertEmpty(Login::find('new'));
        Login::register([
            'id' => 'new', 'plainPassword' => 'password',
        ]);
        $m = Login::findOrFail('new');
        $this->assertEquals('new', $m->loginId);
        $this->assertTrue(password_verify('password', $m->password));
    }

    public function testChange()
    {
        // 正常系
        $this->fixtures->login('any')->save();
        Login::findOrFail('any')->change('testAny');
        $m = Login::findOrFail('any');
        $this->assertEquals('any', $m->id);
        $this->assertEquals('testAny', $m->loginId);

        // 自身に対する同名変更
        Login::findOrFail('any')->change('testAny');
        $m = Login::findOrFail('any');
        $this->assertEquals('any', $m->id);
        $this->assertEquals('testAny', $m->loginId);

        // 重複ID
        try {
            Login::findOrFail('any')->change('test');
            $this->fail();
        } catch (ValidationException $e) {
            $this->assertEquals(ErrorKeys::DUPLICATE_ID, $e->getMessage());
        }
    }

    public function testChangePassword()
    {
        $m = Login::findOrFail('test')->changePassword('changed');
        $this->assertTrue(password_verify('changed', $m->password));
    }

}
