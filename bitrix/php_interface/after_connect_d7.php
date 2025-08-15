<?php
$this->queryExecute("SET sql_mode=''");
$this->queryExecute("SET NAMES 'utf8mb4'");
$this->queryExecute("SET collation_connection = 'utf8mb4_unicode_ci'");
$connection = Bitrix\Main\Application::getConnection();
$connection->queryExecute("SET sql_mode=''");
$connection->queryExecute("SET innodb_strict_mode=0");
$connection = Bitrix\Main\Application::getConnection();
$connection->queryExecute("SET LOCAL time_zone='".date('P')."'");