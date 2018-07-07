<?php

class PhORMTest extends PholdBoxTestBase
{
    protected $db;

    protected function setUp()
    {
		parent::setUp();
		$SYSTEM = array();
		$GLOBALS['SYSTEM'] = &$SYSTEM;
        $GLOBALS['rc'] = null;
        
        //set up the db config for the test database
        $SYSTEM["dsn"] = array();
        $SYSTEM["dsn"]["default"] = "pholdbox";
        $SYSTEM["dsn"]["pholdbox"] = array();
        $SYSTEM["dsn"]["pholdbox"]["connection_string"] = array("mysql:host=127.0.0.1;dbname=pholdbox", "root", "root");
        //get a db connection so we can create the test table.
        $connectionString = $SYSTEM["dsn"]["pholdbox"]["connection_string"];
        $this->db = new \PDO($connectionString[0], $connectionString[1], $connectionString[2], array(\PDO::ATTR_PERSISTENT => true));
        
        //create testdb 
        $this->createTestDB();
    }

    protected function tearDown()
    {
        $this->removeTestDB();
        parent::tearDown();
    }

    /**
     * Creates the test db
     *
     * @return void
     */
    protected function createTestDB()
    {
        $sql = "CREATE TABLE `phorm_test` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(25) DEFAULT NULL,
            `title` varchar(25) DEFAULT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        
        $this->db->query($sql);
    }

    /**
     * removes the test db
     *
     * @return void
     */
    protected function removeTestDB()
    {
        $sql = "DROP TABLE `pholdbox`.`phorm_test`;";
        $this->db->query($sql);
    }

    //start of unit tests

    /**
     * @covers ::__call
     * @covers ::getValue
     * @covers ::setValue
     * @return void
     */
    public function testGetandSet()
    {
        $mock = $this->getMockBuilder('PhORM_Mock')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $mock->setName("bar");
        $this->assertEquals("bar", $mock->getName());
    }

    public function testGetPhORMTable()
    {
        $mock = $this->getMockBuilder('PhORM_Mock')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        
        $this->assertEquals("phorm_test", $mock->getPhORMTable());
    }

    public function testClear()
    {
        $mock = $this->getMockBuilder('PhORM_Mock')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        
        $mock->setName("foo");
        $mock->clear();
        $this->assertEquals(null, $mock->getName());
    }

    public function testToObject()
    {
        $result = new \stdClass();
        $result->id = '';
        $result->name = 'foo';
        $result->title = 'bar';

        $mock = $this->getMockBuilder('PhORM_Mock')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        
        $mock->setName("foo");
        $mock->setTitle("bar");
        
        $this->assertEquals($result, $mock->toObject());
    }

    //start of integration tests
    public function testQuery()
    {
        $mock = $this->getMockBuilder('PhORM_Mock')
            ->setMethods(null)
            ->getMock();
        
        $sql = "select count(*) from phorm_test";
        $result = $mock->query($sql);
        $this->assertEquals(1, $result->rowcount());
        $this->assertEquals(0, $result->fetch()[0]);
    }

    public function testSave()
    {
        $mock = $this->getMockBuilder('PhORM_Mock')
            ->setMethods(null)
            ->getMock();
        
        $mock->setName("Tyrion");
        $mock->setTitle("Dwarf");
        $mock->save();

        $sql = "select * from phorm_test";
        $result = $mock->query($sql);
        $this->assertEquals(1, $result->rowcount());
        $item = $result->fetch();

        $this->assertEquals(1, $mock->getId());
        $this->assertEquals(1, $item["id"]);
        $this->assertEquals("Tyrion", $item["name"]);
        $this->assertEquals("Dwarf", $item["title"]);
    }

    public function testDelete()
    {
        $mock = $this->getMockBuilder('PhORM_Mock')
            ->setMethods(null)
            ->getMock();
        
        $mock->setName("Tyrion");
        $mock->setTitle("Dwarf");
        $mock->save();

        $mock->delete();

        $sql = "select * from phorm_test";
        $result = $mock->query($sql);
        $this->assertEquals(0, $result->rowcount());
        $this->assertFalse($result->fetch());
    }

    public function testSaveAndUpdate()
    {
        $mock = $this->getMockBuilder('PhORM_Mock')
            ->setMethods(null)
            ->getMock();
        
        $mock->setName("Tyrion");
        $mock->setTitle("Dwarf");
        $mock->save();
        $mock->setTitle("King");
        $mock->save();

        $sql = "select * from phorm_test";
        $result = $mock->query($sql);
        $this->assertEquals(1, $result->rowcount());
        $item = $result->fetch();

        $this->assertEquals(1, $item["id"]);
        $this->assertEquals("Tyrion", $item["name"]);
        $this->assertEquals("King", $item["title"]);
    }

    public function testLoad()
    {
        $mock = $this->getMockBuilder('PhORM_Mock')
            ->setMethods(null)
            ->getMock();
        
        $mock->setName("Tyrion");
        $mock->setTitle("Dwarf");
        $mock->save();
        $mock->clear();

        $mock->setId(0);
        $mock->load();

        $sql = "select * from phorm_test";
        $result = $mock->query($sql);
        $this->assertEquals(1, $result->rowcount());
        $item = $result->fetch();

        $this->assertEquals(1, $item["id"]);
        $this->assertEquals("Tyrion", $item["name"]);
        $this->assertEquals("Dwarf", $item["title"]);
    }

    public function testLoad_multi()
    {
        $mock = $this->getMockBuilder('PhORM_Mock')
            ->setMethods(null)
            ->getMock();
        
        $mock->setName("Tyrion");
        $mock->setTitle("Dwarf");
        $mock->save();
        $mock->clear();

        $mock->setId(0);
        $mock->setName("Tyrion");
        $mock->load();

        $sql = "select * from phorm_test";
        $result = $mock->query($sql);
        $this->assertEquals(1, $result->rowcount());
        $item = $result->fetch();

        $this->assertEquals(1, $item["id"]);
        $this->assertEquals("Tyrion", $item["name"]);
        $this->assertEquals("Dwarf", $item["title"]);
    }

    public function testLoad_fail()
    {
        $mock = $this->getMockBuilder('PhORM_Mock')
            ->setMethods(null)
            ->getMock();
        
        $mock->setName("Tyrion");
        $mock->setTitle("Dwarf");
        $mock->save();
        $mock->clear();

        $mock->setName("Hound");
        $mock->load();

        $this->assertEquals('', $mock->getId());        
    }

    public function testLoad_fts()
    {
        $mock = $this->getMockBuilder('PhORM_Mock')
            ->setMethods(null)
            ->getMock();
        
        $mock->setName("Tyrion");
        $mock->setTitle("Dwarf");
        $mock->save();
        $mock->clear();

        $mock->setName("ri");
        $mock->load(true);

        $this->assertEquals(1, $mock->getId());        
    }

    public function testBulkSave()
    {
        $mock = $this->getMockBuilder('PhORM_Mock')
            ->setMethods(null)
            ->getMock();
        
        $mock1 = $this->getMockBuilder('PhORM_Mock')
            ->setMethods(null)
            ->getMock();
        
        $mock2 = $this->getMockBuilder('PhORM_Mock')
            ->setMethods(null)
            ->getMock();
        
        $mock->setName("Tyrion");
        $mock->setTitle("Dwarf");

        $mock1->setName("Jon");
        $mock1->setTitle("Bastard");

        $mock2->setName("Rob");
        $mock2->setTitle("King of the North");

        $people = [$mock, $mock1, $mock2];

        $test = $mock1->bulkSave($people);

        $sql = "select * from phorm_test";
        $result = $mock->query($sql);
        $this->assertEquals(3, $result->rowcount());        
    }

    public function testBulkLoad()
    {
        $mock = $this->getMockBuilder('PhORM_Mock')
            ->setMethods(null)
            ->getMock();
        
        $mock1 = $this->getMockBuilder('PhORM_Mock')
            ->setMethods(null)
            ->getMock();
        
        $mock2 = $this->getMockBuilder('PhORM_Mock')
            ->setMethods(null)
            ->getMock();
        
        $mock->setName("Tyrion");
        $mock->setTitle("Dwarf");

        $mock1->setName("Jon");
        $mock1->setTitle("Bastard");

        $mock2->setName("Rob");
        $mock2->setTitle("King of the North");

        $people = [$mock, $mock1, $mock2];

        $mock1->bulkSave($people);

        $mock->clear();
        $people = $mock->load();

        $this->assertEquals(3, count($people));
        $this->assertEquals('', $mock->getId());
        $this->assertEquals(1, $people[0]->getId());
        $this->assertEquals(2, $people[1]->getId());
        $this->assertEquals(3, $people[2]->getId());
    }

    public function testBulkUpdate()
    {
        $mock = $this->getMockBuilder('PhORM_Mock')
            ->setMethods(null)
            ->getMock();
        
        $mock1 = $this->getMockBuilder('PhORM_Mock')
            ->setMethods(null)
            ->getMock();
        
        $mock2 = $this->getMockBuilder('PhORM_Mock')
            ->setMethods(null)
            ->getMock();
        
        $mock->setName("Tyrion");
        $mock->setTitle("Dwarf");

        $mock1->setName("Jon");
        $mock1->setTitle("Bastard");

        $mock2->setName("Rob");
        $mock2->setTitle("King of the North");

        $people = [$mock, $mock1, $mock2];

        $mock1->bulkSave($people);

        $mock->load();
        $mock1->load();
        $mock2->load();
        
        $mock->setTitle("King");
        $mock1->setTitle("Knows Nothing");
        $mock2->setTitle("Dead");

        $people = [$mock, $mock1, $mock2];
        $mock1->bulkSave($people);

        $mock->clear();
        $people = $mock->load();

        $this->assertEquals(3, count($people));
        $this->assertEquals('', $mock->getId());
        $this->assertEquals("King", $people[0]->getTitle());
        $this->assertEquals("Knows Nothing", $people[1]->getTitle());
        $this->assertEquals("Dead", $people[2]->getTitle());
    }
}

class PhORM_Mock extends system\PhORM
{
    public $ORM = array(
        "tableName"=>"phorm_test",
        "dsn"=>"",
        "columns"=>array("id", "name", "title"),
        "types"=>array("int(1)", "varchar(25)", "varchar(25)"),
        "values"=>array()
    );
}