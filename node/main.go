package main

import (
	"log"
	"github.com/pilot114/node/modules"
)

// точка входа
func main() {
	log.Printf("Node init...")

	// инициализируем ядро программы, загружаем конфигурации
	var core node.NodeCore
	core.Init("config.json")
	log.Printf("Node role: %s", core.Config.Role)
	log.Println(core.Config)

	// запускаем модули через хелперы ядра
	// В каждом модуле стартуют нужные корутины-обработчики
	core.RunDockerModule()
	core.RunNetworkModule()
	core.RunTasksModule()
	// core.runCzmqModule()

	// ожидание флага завершения
	log.Println("Node started!")
	<-core.Done
	log.Println("Node stopped")
}
