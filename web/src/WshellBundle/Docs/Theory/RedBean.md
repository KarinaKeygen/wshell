### Базовые операции

Создаем Bean (вторым параметром можно указать количество), добавляем свойства, сохраняем,
загружаем, удаляем:

    $post = R::dispense('post');
    $post->text = 'Hello World'; // string
    $post->create = '1995-12-05'; // date
    // or $post['create'] = '1995-12-05';
    $id = R::store($post);
    $post = R::load('post',$id);
    R::trash($post);

Индексы и ограничения должны быть добавлены вручную!  
Если load ничего не нашел, возвращается пустой bean с id=0.  
Сущность может быть только строкой из букв в нижнем регистре.  
Свойства в БД будут сохраняться в snake_case.

Создание нескольких сущностей:

    list($book, $page) = R::dispenseAll( 'book,page*2' );

Обновление из базы:

    $bean = $bean->fresh();

Загрузка пачки:

    $ids = [1,2,3,4];
    $books = R::loadAll( 'book', $ids );

Если id не известен, ищем по свойствам:

    $books = R::find( 'book', ' title LIKE ? ', [ 'Learn to%' ] );
    $books = R::findAll( 'book', ' ORDER BY title ASC ' );
    $books = R::findAll( 'book' );
    $book  = R::findOne( 'book', ' rating < 2 ');

Количество, можно с условием:

    $numOfBooks = R::count( 'book' );
    $numOfBooks = R::count( 'book', ' pages > ? ', [ 250 ] );

Удаление, можно пачкой:

    R::trash( $book );
    R::trashAll( $books );

Уничтожить таблицу/базу:

    R::wipe( 'book' );
    R::nuke();
    


### Простые отношения

Отношения определяют связь между сущностями.  
Простые отношения: 1:N и N:1, fixed, N:N

Связать 1:N :

    $shop->ownProductList[] = $vase;
    // $shop->ownProductList = $vases;
    R::store( $shop );
    
Теперь можно делать так:

    $vases = $shop->ownProductList;
    $first = reset( $shop->ownProductList );
    $last = end( $shop->ownProductList );
    foreach( $shop->ownProductList as $product ) {...}
    
Удаление связей:

    unset( $store->ownProductList[$id] ); // one
    $store->ownProductList = array(); // all
    R::store( $shop );
    
Жесткая связь (эксклюзивное владение):

    $shop->xownProductList = array();
    R::store( $shop );
    
Получить N:1 (другими словами, получить родителя):

    $shop = $product->shop;
    $product->shop = NULL; //removes product from shop

fixed - интересное отношение, которое может создавать разные именованные
ссылки (алиасы) от одного bean к другим beans одного типа.

    $c = R::dispense( 'course' );

    $c->teacher = R::dispense( 'person' );
    $c->student = R::dispense( 'person' );
    
    $id = R::store( $c );
    $c = R::load( 'course', $id );
    
    $teacher = $c->fetchAs('person')->teacher;
    
Теперь у курса есть учитель и ученик. Алиас можно использовать
в обратую сторону:

    $person->alias('teacher')->ownCourseList;
    $person->alias('student')->ownCourseList;

Теперь создадим N:N :

    list($vase, $lamp) = R::dispense('product', 2);

    $tag = R::dispense( 'tag' );
    $tag->name = 'Art Deco';

    $vase->sharedTagList[] = $tag;
    $lamp->sharedTagList[] = $tag;
    R::storeAll( [$vase, $lamp] );

Для этого типа отношений создаются перекрестные таблицы вида 'product_tag'.
Тут алиасы создавать нельзя, все ссылки всегда уникальны.


### Сложные отношения

Сложные отношения: link, aggregations, via(N:M), self N:M, 1:1.

Link позволяет прикрепить данные к ссылке:

    list($e, $p) = R::dispenseAll('employee,project');
    $p->link( 'employee_project', [
        'role' => 'director'
    ] )->project = $p;
    
Aggregations(N11N) позволяет трактовать два N:1 отношения как одно N:N,
таким образом получая преимущества общего списка.
Например, так можно загрузить все targets, имяющие в связях quests:

    $targets = $quest1->aggr( 'ownQuestTargetList', 'target', 'quest' );
    
Via похож на aggregations, но с помощью него можно получить объединение
отношений (а не пересечение):
    
    $participant->project = $project;
    $participant->employee = $lisa;
    $participant->role = 'developer';
    R::store( $participant );

    //get all associated employees via the participants (includes $lisa)
    $employees = $project
        ->via( 'participant' )
        ->sharedEmployeeList;
     
Self N:M это просто ссылки на beans того же типа:

    $friends = $friend->sharedFriend;
    
1:1 используется нечасто, тем не менее можно получить несколько сущносей
с одним и тем же ключом используя конструкцию:

    list( $author, $bio ) = R::loadMulti( 'author,bio', $id );


### Модели

Стандартные действия с сохраняемыми сущностями можно расширять; также
можно добавлять свои методы.

В обычных ORM обычно создается специальный класс-обертка, с которым мы
должны работать вместо стандартного. RedBean работает немного по-другому:
мы создаем класс Model_{имя}, RedBean ищет его в текущей области видимости,
если находит - "сливает" его функционал со стандартным.

    class Model_Band extends RedBean_SimpleModel {
    
        // access to bean: $this->bean
        
    }

При использовании неймспейсов нужно указать путь до моделей таким образом:

    define('REDBEAN_MODEL_PREFIX', '\\Model\\');
    
Стандартные методы:

    R::store() invokes update() and after_update(),
    R::load() invokes open(),
    R::trash() invokes delete() and after_delete(),
    R::dispense() invokes dispense().