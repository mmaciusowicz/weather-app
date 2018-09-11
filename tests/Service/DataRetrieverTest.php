<?php
namespace App\Tests\Service;

use App\Service\DataRetriever;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DataRetrieverTest extends KernelTestCase {

    /**
     * @var DataRetriever
     */
    private $dataRetriever;

    /**
     * @expectedException Error
     */
    public function testFailingRetrieveInvalidUrlSyntax() {
        $this->dataRetriever->retrieve('localhost.localdomain');
    }

    /**
     * @expectedException Error
     */
    public function testFailingRetrieveNonexistentUrl() {
        $this->dataRetriever->retrieve('http://localhost/this/does/not/exist/really-it-does-not');
    }

    protected function setUp() {
        $this->dataRetriever = new DataRetriever();
    }

    protected function tearDown() {
        $this->dataRetriever = null;
    }

}
