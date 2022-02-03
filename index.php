<?php
$db = new SQLite3('school.db');

$version = $db->querySingle('SELECT SQLITE_VERSION()');

echo "<br />version: " . $version . "<br />";
#===============================================
# Create table
#===============================================
echo "<hr /><h3>Create Table</h3>";

$SQL_create_table = "CREATE TABLE IF NOT EXISTS Students (
    StudentId VARCHAR(10) NOT NULL,
    FirstName VARCHAR(80),
    LastName VARCHAR(80),
    School VARCHAR(50),
    PRIMARY KEY (StudentId)
);";

echo "<p>$SQL_create_table</p>";

$db->exec($SQL_create_table);
#===============================================
# Insert sample data
#===============================================
// echo "<hr /><h3>Insert sample data</h3>";
// $SQL_insert_data = "INSERT INTO Students (StudentId, FirstName, LastName, School)
// VALUES
// ('A00111111', 'Tom', 'Max', 'Science'),
// ('A00222222', 'Ann', 'Fay', 'Mining'),
// ('A00333333', 'Joe', 'Sun', 'Nursing'),
// ('A00444444', 'Sue', 'Fox', 'Computing'),
// ('A00555555', 'Ben', 'Ray', 'Mining')
// ";

// echo "<p>$SQL_insert_data
// </p>";

// $db->exec($SQL_insert_data);

#===============================================
# Query data using column names
#===============================================
echo "<hr /><h3>Query data</h3>";

$res = $db->query('SELECT * FROM Students');

while ($row = $res->fetchArray()) {
    echo "{$row['StudentId']} {$row['FirstName']} {$row['LastName']}  {$row['School']}<br />";
}

#===============================================
# Parameterized statement with Question marks
#===============================================
echo "<hr /><h3>Parameterized statement with Question marks</h3>";
$stm = $db->prepare('SELECT * FROM Students WHERE StudentId = ?');
$stm->bindValue(1, "A00333333", SQLITE3_TEXT); //bind value is not 0 value based index

$res = $stm->execute();

$row = $res->fetchArray(SQLITE3_NUM);
echo "<p>{$row[0]} {$row[1]} {$row[2]} {$row[3]}</p>"; //0 value based index

#===============================================
# Parameterized statements with named placeholders
#===============================================
echo "<hr /><h3>Parameterized statements with named placeholders</h3>";

$stm = $db->prepare('SELECT * FROM Students WHERE StudentId = :id');
$stm->bindValue(':id', "A00555555", SQLITE3_TEXT);

$res = $stm->execute();

$row = $res->fetchArray(SQLITE3_NUM);
echo "<p>{$row[0]} {$row[1]} {$row[2]} {$row[3]}</p>";

#===============================================
# bind_param
#===============================================
echo "<hr /><h3>bind_param</h3>";
$sql = "";
$sql .= 'SELECT * FROM Students';
$sql .= ' WHERE FirstName = ? AND LastName = ?';

echo "<p>$sql</p>";

$stm = $db->prepare( $sql );

$firstName = 'Sue';
$lastName = 'Fox';

$stm->bindParam(1, $firstName); // $firstname was not declared before
$stm->bindParam(2, $lastName);

$res = $stm->execute();

$row = $res->fetchArray(SQLITE3_NUM);
echo "<p>{$row[0]} {$row[1]} {$row[2]} {$row[3]}</p>";

#===============================================
# Meta data - number of columns
#===============================================
echo "<hr /><h3>Meta data - number of columns</h3>";
$res = $db->query("SELECT FirstName, LastName FROM Students");
$cols = $res->numColumns();

echo "<p>There are {$cols} columns in the result set.</p>";

#===============================================
# Meta data - column names
#===============================================
echo "<hr /><h3>Meta data - column names</h3>";
$res = $db->query("PRAGMA table_info(Students)");

while ($row = $res->fetchArray(SQLITE3_NUM)) {
    echo "<p>{$row[0]} {$row[1]} {$row[2]} {$row[3]}</p>";
}

#===============================================
# Meta data - another way to find column names
#===============================================
echo "<hr /><h3>Meta data - another way to find column names</h3>";

$res = $db->query("SELECT * FROM Students");

$col0 = $res->columnName(0);
$col1 = $res->columnName(1);
$col2 = $res->columnName(2);
$col3 = $res->columnName(3);


$header = sprintf("%-10s %s %s %s\n", $col0, $col1, $col2, $col3);
echo "<p>$header</p>";

while ($row = $res->fetchArray()) {
    $line = sprintf("<p>%-10s %s %s %s</p>", $row[0], $row[1], $row[2], $row[3]);
    echo $line;
}

#===============================================
# Meta Data -List tables in the database
#===============================================
echo "<hr /><h3>Meta Data -List tables in the database</h3>";

$res = $db->query("SELECT name FROM sqlite_master WHERE type='table'");

$cols = $res->numColumns();

echo "<p>There are {$cols} columns in the result set.</p>";

while ($row = $res->fetchArray(SQLITE3_NUM)) {
    echo "<p>{$row[0]}</p>";
}

#===============================================
# Rows that were modified, inserted, or deleted
#===============================================
echo "<hr /><h3>Rows that were modified, inserted, or deleted</h3>";
$SQL_insert_data = "INSERT INTO Students (StudentId, FirstName, LastName, School)
VALUES
('A00666666', 'Tim', 'Day', 'Science'),
('A00777777', 'Zoe', 'Fry', 'Mining'),
('A00888888', 'Jim', 'Roy', 'Nursing'),
('A00999999', 'Fay', 'Lot', 'Computing')
";

$db->exec($SQL_insert_data);
$changes = $db->changes();
echo "<p>The INSERT statement added $changes rows</p>";

# close database
$db->close();

?>