<?php
namespace App\Models\Account;

use App\Context\ErrorKeys;
use App\Context\ValidationException;
use App\Tests\EntityTestSupport;

class AccountTest extends EntityTestSupport
{
    protected function initialize()
    {
        $this->fixtures->acc('normal')->save();
    }

    public function testRegister()
    {
        // 通常登録
        $this->assertEmpty(Account::find('new'));
        Account::register([
            'id' => 'new', 'name' => 'name', 'mail' => 'new@example.com',
            'plainPassword' => 'password',
        ]);
        $m = Account::findOrFail('new');
        $this->assertEquals('name', $m->name);
        $this->assertEquals('new@example.com', $m->mail);
        $l = Login::findOrFail('new');
        $this->assertTrue(password_verify('password', $l->password));

        // 同一ID重複
        try {
            Account::register([
                'id' => 'normal', 'name' => 'name', 'mail' => 'normal@example.com',
                'plainPassword' => 'password',
            ]);
            $this->fail();
        } catch (ValidationException $e) {
            $this->assertEquals(ErrorKeys::DUPLICATE_ID, $e->getMessage());
        }
    }

    public function testChange()
    {
        Account::findOrFail('normal')->change([
            'name' => 'changed', 'mail' => 'changed@example.com',
        ]);
        $m = Account::findOrFail('normal');
        $this->assertEquals('changed', $m->name);
        $this->assertEquals('changed@example.com', $m->mail);
    }

    public function testLoadValid()
    {
        // 通常時取得
        $m = Account::loadValid('normal');
        $this->assertEquals('normal', $m->id);
        $this->assertEquals(AccountStatusType::NORMAL, $m->statusType);

        // 退会時取得
        $withdrawal = $this->fixtures->acc('withdrawal');
        $withdrawal->statusType = AccountStatusType::WITHDRAWAL;
        $withdrawal->save();
        try {
            Account::loadValid('withdrawal');
            $this->fail();
        } catch (ValidationException $e) {
            $this->assertEquals('error.Account.loadValid', $e->getMessage());
        }
    }

}
