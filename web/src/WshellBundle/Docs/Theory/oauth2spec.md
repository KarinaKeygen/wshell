RFC 6749
RFC 6750

### Introduction

OAuth дает важные преимущества по сравнению с другими
способами предоставить права на ресурсы другим сервисам:

* Сторонние сервисы не хранят персональные данные
* Можно устанавливать различные уровни доступа
* Пользователь может отозвать доступ

Access token - единственное, что нужно стороннему сервису
для получения данных.

Участники:
resource owner   	 - пользователь
client			 	 - OAuth клиент (тот, кому нужны ресурсы)
resource server	 	 - место, где хранятся ресурсы
authorization server - OAuth сервер

resource server и authorization server могут быть одной сущностью.
OAuth предназначен только для работы поверх HTTP(S).
На данный момент OAuth 2.0 полностью совместим только с TLS 1.0

Общая схема протокола:
	 +--------+                               +---------------+
     |        |--(A)- Authorization Request ->|   Resource    |
     |        |                               |     Owner     |
     |        |<-(B)-- Authorization Grant ---|               |
     |        |                               +---------------+
     |        |
     |        |                               +---------------+
     |        |--(C)-- Authorization Grant -->| Authorization |
     | Client |                               |     Server    |
     |        |<-(D)----- Access Token -------|               |
     |        |                               +---------------+
     |        |
     |        |                               +---------------+
     |        |--(E)----- Access Token ------>|    Resource   |
     |        |                               |     Server    |
     |        |<-(F)--- Protected Resource ---|               |
     +--------+                               +---------------+

A/B - это проверка авторизации пользователя там, где хранятся ресурсы.
В ходе этой проверки пользователь должен дать разрешение (Authorization Grant)
на определенные ресурсы.
Authorization Grant бывает:

	* Authorization Code - выдается пользователю и клиенту, способствует
	его идентификации. Безопаснее.
	* Implicit("неявный") - даётся только пользователю, он не идентифицируется OAuth сервером. Проще.
	* Resource Owner Password Credentials - Пользователь вводит логин/пасс,
	которые в дальнейшем обмениваются на маркер доступа. Это может позволить
	клиенту самостоятельно обновлять права доступа независимо от пользователя,
	но при этом должна быть высокая степень доверия между клиентом и сервером.
	* Client Credentials - когда клиент действует от своего имени, а не от имени пользователя.

С/D - клиент получает маркер доступа, являющийся просто удобной абстракцией.
Маркер доступа можно использовать как способ аутентификации или
использовать как составляющую часть процесса аутентификации.

E/F - Клиент получает соответствующие маркеру данные!

Дополнительная возможность - предоставить маркер обновления доступа (refresh token) на этапе D.
С помощью него клиент сможет самостоятельно обновлять маркер доступа. Refresh token
может передаваться только на oauth сервер и никогда - на resource server.


Схема обновлнения маркера доступа:
  +--------+                                           +---------------+
  |        |--(A)------- Authorization Grant --------->|               |
  |        |                                           |               |
  |        |<-(B)----------- Access Token -------------|               |
  |        |               & Refresh Token             |               |
  |        |                                           |               |
  |        |                            +----------+   |               |
  |        |--(C)---- Access Token ---->|          |   |               |
  |        |                            |          |   |               |
  |        |<-(D)- Protected Resource --| Resource |   | Authorization |
  | Client |                            |  Server  |   |     Server    |
  |        |--(E)---- Access Token ---->|          |   |               |
  |        |                            |          |   |               |
  |        |<-(F)- Invalid Token Error -|          |   |               |
  |        |                            +----------+   |               |
  |        |                                           |               |
  |        |--(G)----------- Refresh Token ----------->|               |
  |        |                                           |               |
  |        |<-(H)----------- Access Token -------------|               |
  +--------+           & Optional Refresh Token        +---------------+

Все просто - когда клиент во время очередного обращения к Resource Server получает сообщение
об ошибке (F), например когда токен просрочен, он меняет refresh token на новый access token
и (опционально) получает ещё один refresh token.




### Client registration

Все клиенты должен быть зарегистрированны на OAuth сервере.
При этом клиенту не обязательно с ним взаимодействовать - механизм
регистрации полностью автономный и находится на OAuth сервере.

##Client Types.
Спецификация опеределяет 2 условных типа клиентов: confidential и public
(используют/не используют механизмы аутентификации). Описание public клиентов
выходит за рамки спецификации.
Клиентом может быть web-приложение, браузер или нативное приложение.

##Client Identifier
Любая уникальная для каждого клиента строка, не является секретной.

##Client Authentication
Любой механизм аутентификации OAuth клиента на  OAuth сервере. (Логин/пароль, сертификат и т.д.)
Обычно это Client Password - секретный пароль клиента.
OAuth сервер должен поддерживать HTTP аутентификацию и может поддерживать аутентификацию
на основе параметров, передаваемых в теле запроса: client_id и client_secret
Эти параметры не рекомендуется передавать в URI.
Любой endpoint с парольной аутентификацией должен работать поверх TLS и иметь
защиту от атаки перебором.




### Protocol Endpoint

На сервере должно быть два специфичных URI:

	* Authorization endpoint - получение разрешения на ресурсы.
	Используется в Authorization Code и Implicit. Какой именно тип используется, сервер узнает по
	обязательному параметру response_type = 'code' || 'token'.
	Если юзер подтвердил выдачу ресурсов, отсюда мы отправляем его на клиента (redirect_uri).
	redirect_uri обязателен для public и implicit клиентов и желателен для
	всех остальных из соображений безопасности. Клиент может иметь несколько redirect_uri.
	
	* Token endpoint - обмен auth token на access token

+ Redirection endpoint на клиенте (конечно, если клент - web приложение)



### Obtaining Authorization

Для получения Request Token нужно использовать один из 4 стандартных
grant type(Также OAuth разрешает добавление новых grant types):

  * authorization code - используется для получения AT и RT.
  обязательно использование Redirect Uri

  Схема авторизации для authorization code
     +----------+
     | Resource |
     |   Owner  |
     |          |
     +----------+
          ^
          |
         (B)
     +----|-----+          Client Identifier      +---------------+
     |         -+----(A)-- & Redirection URI ---->|               |
     |  User-   |                                 | Authorization |
     |  Agent  -+----(B)-- User authenticates --->|     Server    |
     |          |                                 |               |
     |         -+----(C)-- Authorization Code ---<|               |
     +-|----|---+                                 +---------------+
       |    |                                         ^      v
      (A)  (C)                                        |      |
       |    |                                         |      |
       ^    v                                         |      |
     +---------+                                      |      |
     |         |>---(D)-- Authorization Code ---------'      |
     |  Client |          & Redirection URI                  |
     |         |                                             |
     |         |<---(E)----- Access Token -------------------'
     +---------+       (w/ Optional Refresh Token)

  Это типичный и наиболее распространенный grant type.
  Поля в пункте (A):
  response_type="code"
  client_id = xxxxxxxxxxx
  redirect_uri = yyyyyyyyyyy
  scope&state опционально

  ПРИМЕР: GET /authorize?response_type=code&client_id=s6BhdRkqt3&state=xyz&redirect_uri=https%3A%2F%2Fclient%2Eexample%2Ecom%2Fcb HTTP/1.1 Host: server.example.com
  Поля в пункте (С):
  code=xxxxxxxxxxx
  state=yyyyyyyy

  Поля в пункте (D):
  grant_type + code + redirect_uri + client_id обязательны
  
  Пример для (E):
  {
   "access_token":"2YotnFZFEjr1zCsicMWpAA",
   "token_type":"example",
   "expires_in":3600,
   "refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA",
   "example_parameter":"example_value"
  }


  * implicit
  * resource owner password credentials
  * client credentials



### Issuing an Access Point
### Refreshing Access Point
### Accessing Protected Resources
### Extensibility
### Native Application
### Security Considerations
### IANA Considerations
### References and appendix