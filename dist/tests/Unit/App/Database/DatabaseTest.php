<?php

use App\Exceptions\SqlException;
use App\Registries\LoggerRegistry;
use App\Utils\Logger;

class DatabaseTest extends \PHPUnit\Framework\TestCase {
    protected function tearDown(): void {
        Database::setEchoQueries(false);
        Database::dropTable('test_table');
        Settings::delete('foo');
        Settings::delete('foo2');

        Database::select($_ENV['DB_DATABASE']);
    }

    public function testIsConnectedReturnsTrue(): void {
        $this->assertTrue(Database::isConnected());
    }

    public function testIsSchemaSelectedReturnsTrue(): void {
        $this->assertTrue(Database::isSchemaSelected());
    }

    public function testIsSchemaSelectedReturnsFalse(): void {
        Database::select('nothing');
        $this->assertFalse(Database::isSchemaSelected());
    }

    public function testIsConnectedReturnsFalse(): void {
        $oldConnection = Database::getConnection();

        Database::setConnection(null);

        $this->assertFalse(Database::isConnected());

        Database::setConnection($oldConnection);
    }

    public function testGetAllTables(): void {
        $tables = Database::getAllTables();
        $prefix = $_ENV['DB_PREFIX'];
        $this->assertGreaterThanOrEqual(20, count($tables));

        $this->assertContains("{$prefix}content", $tables);
        $this->assertContains("{$prefix}settings", $tables);
        $this->assertContains("{$prefix}users", $tables);
    }

    public function testGetServerVersion(): void {
        $version = Database::getServerVersion();
        $version = preg_replace('/[^0-9.].*/', '', $version);
        $this->assertTrue(\App\Utils\VersionComparison::compare($version, '5.5.3', '>='));
    }

    public function testAnyReturnsTrue(): void {
        $result = Database::query("select id from {prefix}settings where value <> ''", true);
        $this->assertTrue(Database::any($result));
    }

    public function testAnyReturnsFalse(): void {
        $result = Database::query('select id from {prefix}settings where value <> value', true);
        $this->assertFalse(Database::any($result));
    }

    public function testGetColumnNames(): void {
        $columns = Database::getColumnNames('users', true);
        $this->assertGreaterThanOrEqual(18, count($columns));
        $this->assertContains('username', $columns);
        $this->assertContains('about_me', $columns);
        $this->assertContains('homepage', $columns);
        $this->assertContains('password', $columns);
    }

    public function testGetNumRowsAny(): void {
        $result = Database::query("select id from {prefix}settings where name in ('homepage_title', 'frontpage', 'installed_at')", true);
        $this->assertEquals(3, Database::getNumRows($result));
    }

    public function testGetNumRowsZero(): void {
        $result = Database::query("select id from {prefix}settings where name in ('this_is_not_a_setting')", true);
        $this->assertEquals(0, Database::getNumRows($result));
    }

    public function testGetLastError(): void {
        $this->expectException(SqlException::class);
        // this sql fails always
        $result = Database::query('select devil from hell', true);
        $this->assertFalse($result);

        $error = Database::getLastError();

        $this->assertStringStartsWith('Table', $error);
        $this->assertStringEndsWith("doesn't exist", $error);
    }

    public function testError(): void {
        // this sql fails always
        try {
            $result = Database::query('select devil from hell', true);
            $this->assertFalse($result);
        } catch (Exception $e) {
            $error = Database::error();

            $this->assertStringStartsWith('Table', $error);
            $this->assertStringEndsWith("doesn't exist", $error);
        }
    }

    public function testGetError(): void {
        LoggerRegistry::register('sql_log', $this->getSQLLogger());
        try {
            // this sql fails always
            $result = Database::query('select devil from hell', true);
            $this->assertFalse($result);
        } catch (SqlException $e) {
            $error = Database::getError();

            $this->assertStringStartsWith('Table', $error);
            $this->assertStringEndsWith("doesn't exist", $error);
        }
        LoggerRegistry::unregister('sql_log');
    }

    public function testSelectAll(): void {
        $allSettings = Database::selectAll('settings');
        $this->assertTrue(Database::any($allSettings));
    }

    public function testDropTable(): void {
        Database::query('CREATE TABLE {prefix}test_table (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        firstname VARCHAR(30) NOT NULL,
                        lastname VARCHAR(30) NOT NULL,
                        email VARCHAR(50),
                        reg_date TIMESTAMP)', true);

        $prefix = $_ENV['DB_PREFIX'];

        $this->assertContains("{$prefix}test_table", Database::getAllTables());
        Database::dropTable('test_table');
        $this->assertNotContains("{$prefix}test_table", Database::getAllTables());
    }

    public function testDropColumn(): void {
        Database::query('CREATE TABLE {prefix}test_table (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        firstname VARCHAR(30) NOT NULL,
                        lastname VARCHAR(30) NOT NULL,
                        email VARCHAR(50),
                        reg_date TIMESTAMP)', true);

        $this->assertContains('reg_date', Database::getColumnNames('test_table'));

        Database::dropColumn('test_table', 'reg_date');
        $this->assertNotContains('reg_date', Database::getColumnNames('test_table'));

        Database::dropTable('test_table');
    }

    public function testGetClientInfo(): void {
        $this->assertStringStartsWith('mysql', Database::getClientInfo());
    }

    public function testGetClientVersion(): void {
        // https://www.w3schools.com/php/func_mysqli_get_client_version.asp
        $this->assertGreaterThanOrEqual(50000, Database::getClientVersion());
    }

    public function testEscapeName(): void {
        $this->assertEquals('`alter`', Database::escapeName('alter'));
        $this->assertEquals('`JohnDoe`', Database::escapeName("'JohnDoe'"));
        $this->assertEquals('`JohnDoe`', Database::escapeName('"JohnDoe"'));
    }

    public function testGetConnectionReturnsMysqliObject(): void {
        $this->assertInstanceOf('mysqli', Database::getConnection());
    }

    public function testIsConnectedReturnsNull(): void {
        $oldConnection = Database::getConnection();

        Database::setConnection(null);

        $this->assertNull(Database::getConnection());

        Database::setConnection($oldConnection);
    }

    public function testGetLastInsertID(): void {
        Database::query("insert into {prefix}settings (name, value)
                         values
                         ('foo2', 'bar')", true);

        $lastInsertId = Database::getLastInsertID();
        $this->assertNotNull($lastInsertId);

        $result = Database::selectAll('settings', [
            'id'
        ], "name = 'foo2'");
        $dataset = Database::fetchObject($result);
        $this->assertEquals($dataset->id, $lastInsertId);

        Settings::delete('foo2');
    }

    public function testGetInsertID(): void {
        Database::query("insert into {prefix}settings (name, value)
                         values
                         ('foo2', 'bar')", true);

        $lastInsertId = Database::getInsertID();
        $this->assertNotNull($lastInsertId);

        $result = Database::selectAll('settings', [
            'id'
        ], "name = 'foo2'");
        $dataset = Database::fetchObject($result);
        $this->assertEquals($dataset->id, $lastInsertId);

        Settings::delete('foo2');
    }

    public function testGetNumFieldCount(): void {
        Database::selectAll('users', ['lastname', 'firstname', 'email']);
        $this->assertEquals(3, Database::getNumFieldCount());

        Database::selectAll('content', ['slug', 'title']);
        $this->assertEquals(2, Database::getNumFieldCount());
    }

    public function testGetAffectedRows(): void {
        for ($i = 1; $i <= 13; $i++) {
            Settings::set("test_setting_{$i}", 1);
        }
        Database::deleteFrom('settings', "name like 'test_setting_%'");
        $this->assertEquals(13, Database::getAffectedRows());
    }

    public function testSelectMinReturnsZero(): void {
        $this->assertEquals(0, Database::selectMin('settings', 'id', '1 = 0'));
    }

    public function testSelectMinReturnsMin(): void {
        $min = Database::selectMin('settings', 'id');
        $max = Database::selectMax('settings', 'id');

        $this->assertIsInt($min);
        $this->assertGreaterThanOrEqual(1, $min);
        $this->assertLessThan($max, $min);
    }

    public function testSelectMaxReturnsZero(): void {
        $this->assertEquals(0, Database::selectMax('settings', 'id', '1 = 0'));
    }

    public function testSelectMaxReturnsMax(): void {
        $min = Database::selectMin('settings', 'id');
        $max = Database::selectMax('settings', 'id');
        $this->assertIsInt($max);
        $this->assertGreaterThan($min, $max);
    }

    public function testSelectAvgReturnsZero(): void {
        $this->assertEquals(0, Database::selectAvg('settings', 'id', '1 = 0'));
    }

    public function testSelectAvgReturnsAvg(): void {
        $min = Database::selectMin('settings', 'id');
        $max = Database::selectMax('settings', 'id');
        $avg = Database::selectAvg('settings', 'id');
        $this->assertIsFloat($avg);

        $this->assertGreaterThan($min, $avg);
        $this->assertLessThan($max, $avg);
    }

    public function testFetchAll(): void {
        $result = Database::query(
            "select * from {prefix}settings where
        name in
        ('default_font', 'frontpage', 'homepage_title')
        order by name",
            true
        );

        $datasets = Database::fetchAll($result);
        $this->assertCount(3, $datasets);
        $this->assertEquals('default_font', $datasets[0]->name);
        $this->assertEquals('frontpage', $datasets[1]->name);
        $this->assertEquals('homepage_title', $datasets[2]->name);
        foreach ($datasets as $dataset) {
            $this->assertNotEmpty($dataset->value);
        }
    }

    public function testGetSqlStrictModeFlags(): void {
        $this->assertCount(6, Database::getSqlStrictModeFlags());
        foreach (Database::getSqlStrictModeFlags() as $flag) {
            $this->assertIsString($flag);
            $this->assertNotEmpty($flag);

            // string must be Uppercase
            $this->assertEquals(strtoupper($flag), $flag);
        }
    }

    public function testTableExistsReturnsTrue(): void {
        $this->assertTrue(Database::tableExists('content'));
        $this->assertTrue(Database::tableExists('settings'), true);
        $this->assertTrue(Database::tableExists(Database::tableName('content'), false));
    }

    public function testTableExistsReturnsFalse(): void {
        $this->assertFalse(Database::tableExists('gibts_echt_nicht'));
        $this->assertFalse(Database::tableExists('content', false));
    }

    public function testFetchArray(): void {
        $query = Database::selectAll('settings');

        while ($row = Database::fetchArray($query)) {
            $this->assertCount(6, $row);
            $this->assertNotEmpty($row['name']);
        }
    }

    public function testFetchRow(): void {
        $query = Database::selectAll('settings');

        while ($row = Database::fetchRow($query)) {
            $this->assertCount(3, $row);
            $this->assertIsNumeric($row[0]);
            $this->assertNotEmpty($row[1]);
        }
    }

    public function testFetchSingleOrDefaultReturnsObject(): void {
        $query = Database::selectAll('settings', [], ' 1 = 1 limit 1');

        $default = new stdClass();
        $default->name = 'default value';

        $row = Database::fetchSingleOrDefault($query, $default);
        $this->assertIsObject($row);
        $this->assertIsNumeric($row->id);
        $this->assertNotEmpty($row->name);
    }

    public function testFetchFirstReturnsNull(): void {
        $query = Database::selectAll('settings', [], '1 = 0');

        $row = Database::fetchFirst($query);
        $this->assertNull($row);
    }

    public function testFetchFirstReturnsObject(): void {
        $query = Database::selectAll('settings', [], '1=1');

        $row = Database::fetchFirst($query);
        $this->assertIsObject($row);
        $this->assertIsNumeric($row->id);
        $this->assertNotEmpty($row->name);
    }

    public function testFetchSingleOrDefaultThrowsException(): void {
        $query = Database::selectAll('settings', []);

        $default = new stdClass();
        $default->name = 'default value';

        $this->expectException(RangeException::class);

        Database::fetchSingleOrDefault($query, $default);
    }

    public function testFetchSingleOrDefaultReturnsDefault(): void {
        $query = Database::selectAll('settings', [], '1 = 0');

        $default = new stdClass();
        $default->name = 'default value';

        $row = Database::fetchSingleOrDefault($query, $default);

        $this->assertIsObject($row);
        $this->assertEquals('default value', $row->name);
    }

    public function testEscapeValueWithFloat(): void {
        $this->assertEquals(2.99, Database::escapevalue(2.99));
    }

    public function testEscapeValueWithTypeInt(): void {
        $this->assertEquals(2, Database::escapevalue(2.99, DB_TYPE_INT));
    }

    public function testEscapeValueWithTypeFloat(): void {
        $this->assertEquals(2.0, Database::escapevalue(2, DB_TYPE_FLOAT));
    }

    public function testEscapeValueWithTypeString(): void {
        $this->assertEquals('123', Database::escapevalue(123, DB_TYPE_STRING));
    }

    public function testEscapeValueWithTypeBool(): void {
        $this->assertEquals(0, Database::escapevalue(false, DB_TYPE_BOOL));
        $this->assertEquals(1, Database::escapevalue(true, DB_TYPE_BOOL));
    }

    public function testEscapeValueWithTypeOther(): void {
        $this->assertInstanceOf(Page::class, Database::escapevalue(new Page(), PHP_INT_MAX));
    }

    public function testfetchFirstOrDefaultReturnsFirst(): void {
        $query = Database::selectAll('settings', []);

        $default = new stdClass();
        $default->name = 'default value';

        $row = Database::fetchFirstOrDefault($query, $default);

        $this->assertIsNumeric($row->id);
        $this->assertNotEmpty($row->name);
    }

    public function testfetchFirstOrDefaultReturnsDefault(): void {
        $query = Database::selectAll('settings', [], '1 = 0');

        $default = new stdClass();
        $default->name = 'default value';

        $row = Database::fetchFirstOrDefault($query, $default);

        $this->assertIsObject($row);
        $this->assertEquals('default value', $row->name);
    }

    public function testFetchSingleReturnsNull(): void {
        $query = Database::selectAll('settings', [], '1 = 0');

        $row = Database::fetchSingle($query);
        $this->assertNull($row);
    }

    public function testFetchSingleThrowsException(): void {
        $query = Database::selectAll('settings');

        $this->expectException(RangeException::class);
        Database::fetchSingle($query);
    }

    public function testFetchSingleReturnsObject(): void {
        $query = Database::selectAll('settings', [], '1=1 limit 1');

        $row = Database::fetchSingle($query);
        $this->assertIsObject($row);
        $this->assertIsNumeric($row->id);
        $this->assertNotEmpty($row->name);
    }

    public function hasMoreResultsReturnsFalse(): void {
        $this->assertFalse(Database::hasMoreResults());
    }

    public function testCreateSelectAndDropSchema(): void {
        $schema = 'tmp_database_' . uniqid();
        $this->assertTrue(Database::createSchema($schema));
        $this->assertTrue(Database::select($schema));

        $this->assertTrue(
            Database::select($_ENV['DB_DATABASE'])
        );

        $this->assertTrue(Database::dropSchema($schema));
    }

    public function testEchoQueriesOutputsSQL(): void {
        Database::setEchoQueries(true);
        ob_start();
        Database::query("select 'foo' as bar");
        $this->assertEquals("select 'foo' as bar\n", ob_get_clean());
    }

    public function testEchoQueriesOutputsNothing(): void {
        Database::setEchoQueries(false);
        ob_start();
        Database::query("select 'foo' as bar");
        $this->assertEmpty(ob_get_clean());
    }

    public function testClose(): void {
        $this->assertTrue(Database::isConnected());

        Database::close();
        $this->assertFalse(Database::isConnected());

        $this->reconnect(true);
        $this->assertTrue(Database::isConnected());

        Database::close();

        $this->reconnect();
    }

    public function testConnectFails(): void {
        Database::close();

        $db_socket = isset($_ENV['DB_SOCKET']) ? (string)$_ENV['DB_SOCKET'] : ini_get('mysqli.default_socket');
        $db_port = (int)($_ENV['DB_PORT'] ?? ini_get('mysqli.default_port'));
        $db_strict_mode = isset($_ENV['DB_STRICT_MODE']) && $_ENV['DB_STRICT_MODE'];

        @$connect = Database::connect($_ENV['DB_SERVER'], $_ENV['DB_USER'], 'invalid_password', $db_port, $db_socket, $db_strict_mode);
        $this->assertNull($connect);

        $this->reconnect();
    }

    public function testMultiQuery(): void {
        Database::setEchoQueries(true);
        ob_start();

        LoggerRegistry::register('sql_log', $this->getSQLLogger());

        Database::multiQuery(
            "select id from {prefix}settings; select 'foo' as bar;"
            . 'show tables;',
            true
        );

        $queries = 0;
        while (Database::hasMoreResults()) {
            Database::loadNextResult();
            $result = Database::storeResult();
            $this->assertInstanceOf(mysqli_result::class, $result);
            $this->assertGreaterThanOrEqual(1, Database::getNumRows($result));
            $queries++;
        }
        $this->assertEquals(3, $queries);
        LoggerRegistry::unregister('sql_log');
        ob_end_clean();
    }

    protected function getSQLLogger(): Logger {
        $path = Path::resolve('ULICMS_LOG/sql_exception');
        return new Logger($path);
    }

    private function reconnect($db_strict_mode = null): void {

        $db_socket = isset($_ENV['DB_SOCKET']) ? (string)$_ENV['DB_SOCKET'] : ini_get('mysqli.default_socket');
        $db_port = (int)($_ENV['DB_PORT'] ?? ini_get('mysqli.default_port'));

        if ($db_strict_mode === null) {
            $db_strict_mode = isset($_ENV['DB_STRICT_MODE']) && $_ENV['DB_STRICT_MODE'];
        }

        Database::connect($_ENV['DB_SERVER'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $db_port, $db_socket, $db_strict_mode);

        Database::select($_ENV['DB_DATABASE']);
    }
}
