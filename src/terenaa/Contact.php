<?php
/**
 * Contact model
 *
 * PHP version 5
 *
 * @category    Utils
 * @author      Krzysztof Janda <k.janda@the-world.pl>
 * @license     https://opensource.org/licenses/MIT MIT
 * @version     1.0
 * @link        https://www.github.com/terenaa/sms-gateway
 *
 */

namespace terenaa\SmsGateway;


class Contact
{
    protected $dbh;

    public function __construct()
    {
        $this->dbh = new \PDO('sqlite:../db/contacts.db3');
        $this->dbh->exec("CREATE TABLE IF NOT EXISTS contact (id INTEGER PRIMARY KEY, `phone` TEXT NOT NULL, `name` TEXT UNIQUE NOT NULL)");
    }

    public function create($phone, $name)
    {
        $stmt = $this->dbh->prepare("INSERT INTO contact (`phone`, `name`) VALUES (:phone, :name)");
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':name', $name);

        return $stmt->execute();
    }

    public function update($phone, $name)
    {
        $stmt = $this->dbh->prepare("UPDATE contact SET `name` = :name WHERE `phone` = :phone");
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':name', $name);

        return $stmt->execute();
    }

    public function delete($name)
    {
        $stmt = $this->dbh->prepare("DELETE FROM contact WHERE `name` = :name");
        $stmt->bindParam(':name', $name);

        return $stmt->execute();
    }

    public function getPhone($name)
    {
        $stmt = $this->dbh->prepare("SELECT phone FROM contact WHERE name = ?");
        $stmt->execute(array($name));
        $phone = $stmt->fetch(\PDO::FETCH_OBJ);

        return $phone ? $phone->phone : null;
    }
}
