package config

var mysqlDB map[string]map[string]interface{}

//func init() {
//
//	mysqlDB = make(map[string]map[string]interface{})
//	mysqlDB["blog"] = make(map[string]interface{})
//	mysqlDB["test"] = make(map[string]interface{})
//
//	mysqlDB["blog"]["MysqlHost"] = "115.28.136.83"
//	mysqlDB["blog"]["MysqlPort"] = "3306"
//	mysqlDB["blog"]["MysqlUser"] = "root"
//	mysqlDB["blog"]["MysqlName"] = "blog"
//	mysqlDB["blog"]["MysqlPassword"] = "zTAPDeeNWmLDW4VL6Hinql1Zf"
//	mysqlDB["blog"]["MysqlCHARSET"] = "utf8mb4"
//	mysqlDB["blog"]["SetMaxOpenConns"] = 100
//	mysqlDB["blog"]["SetMaxIdleConns"] = 10
//
//	mysqlDB["test"]["MysqlHost"] = "115.28.136.83"
//	mysqlDB["test"]["MysqlPort"] = "3306"
//	mysqlDB["test"]["MysqlUser"] = "root"
//	mysqlDB["test"]["MysqlName"] = "test"
//	mysqlDB["test"]["MysqlPassword"] = "zTAPDeeNWmLDW4VL6Hinql1Zf"
//	mysqlDB["test"]["MysqlCHARSET"] = "utf8mb4"
//	mysqlDB["test"]["SetMaxOpenConns"] = 100
//	mysqlDB["test"]["SetMaxIdleConns"] = 10
//}
//
//func GetDBConfig(name string) (db map[string]interface{}) {
//	db = mysqlDB[name]
//	return
//}
