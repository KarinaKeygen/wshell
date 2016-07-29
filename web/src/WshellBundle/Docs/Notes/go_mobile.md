### Введение

Возможность написания android и ios приложений на go появилась с версии 1.5 (19 августа 2015)
Здесь мы последовательно рассмотрим процесс написания таких приложений.

На данный момент есть 2 стратегии написания приложения:
* полностью на Go (native)
* SDK приложение написанное на Java или Objective-C, с биндингом для Go.

Далее будет рассматриваться первый вариант, как более простой.
Для этого нам потребуются:

* go1.5 и выше
* gomobile (https://godoc.org/golang.org/x/mobile/cmd/gomobile)
* знание языка go (https://gobyexample.com/)

Вот что на данный момент можно делать:
* контроль выполнения приложения
* OpenGL ES 2 binding (известная графическая библиотека, заточенная под мобильные устройства)
* управление ассетами (изображения, аудио и пр.файлы)
* управление событиями
* экспериментальные пакеты (OpenAL, audio, fort, sprites, motion sensors)

дальнейшие примеры написаны на основе официальной документации:
https://godoc.org/golang.org/x/mobile


### Lifecicle приложения

Простейший пример:

package main
import (
	"log"
	"golang.org/x/mobile/app"
	"golang.org/x/mobile/event/lifecycle"
	"golang.org/x/mobile/event/paint"
	"golang.org/x/mobile/event/mouse"
	"golang.org/x/mobile/event/size"
	"golang.org/x/mobile/event/touch"
)
func init() {
	app.RegisterFilter(func(e interface{}) interface{} {
		if e, ok := e.(lifecycle.Event); ok {
			switch e.Crosses(lifecycle.StageVisible) {
				case lifecycle.CrossOn:
					start()
				case lifecycle.CrossOff:
					stop()
			}
		}
		return e
	})
}
func main() {
	app.Main(func(a app.App) {
		for e := range a.Events() {
			switch e := app.Filter(e).(type) {
			case lifecycle.Event:
				log.Print("Call lifecycle")
			case mouse.Event:
				log.Print("Call mouse")
			case size.Event:
				// e.WidthPx
				// e.HeightPx
				log.Print("Call size")
			case touch.Event:
				// e.X
				// e.Y
				log.Print("Call touch")
			case paint.Event:
				log.Print("Call OpenGL here.")
				a.EndPaint(e)
			}
		}
	})
}

По сути, тут происходит следующее: мы получаем различные события от приложения,
фильтруем их, и выбираем по типу события соответствующий кейс ("кастуем")

Стандартные эвенты (5 штук): lifecycle, mouse, paint, size, touch
Отдельные пакеты могут регистрировать свои эвенты и фильтры

func RegisterFilter(f func(interface{}) interface{})
RegisterFilter должен вызываться только в init функции. (???)

