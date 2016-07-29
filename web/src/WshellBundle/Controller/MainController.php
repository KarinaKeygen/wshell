<?php

namespace WshellBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Parsedown;

class MainController extends Controller
{
    public function newsAction()
    {
        return $this->render('WshellBundle:Main:news.html.twig', [
          'active1' => 'active',
        ]);
    }

    public function nablaAction()
    {
        return $this->render('WshellBundle:Main:nabla.html.twig', [
          'active2' => 'active',
        ]);
    }
    public function termAction()
    {
      $server = new Junior\Server(new WshellBundle\Terminal\Terminal());
      return JsonResponse($server->process());
    }

    public function chaptersAction()
    {
      $chapters = [
          [
              'anno'  => 'Юниты представляют собой настраиваемый набор инструментов для широкого круга вычислительных задач',
              'title' => 'Юниты',
              'link'  => '/units/chain',
              'img'   => 'img/chapters/make.png'
          ],
         // [
         //     'anno' => 'Тут можно поболтать с разработчиком и другими пользователями',
         //     'title' => 'Чаты',
         //     'link' => '/chats',
         //     'img' => 'img/chapters/bla.png'
         // ],
         // [
         //     'anno' => 'Для любопытных',
         //     'title' => 'Квесты',
         //     'link' => '/quests',
         //     'img' => 'img/chapters/qu.png'
         // ],
         [
             'anno' => 'Динамическая карта',
             'title' => 'Карта',
             'link' => '/map',
             'img' => 'img/chapters/map.png'
         ],
         // [
         //     'anno' => 'Fight!',
         //     'title' => 'Battle Monkeys',
         //     'link' => '/battle',
         //     'img' => 'img/chapters/bm.png'
         // ],
      ];

        return $this->render('WshellBundle:Main:chapters.html.twig', [
          'active3' => 'active',
          'chapters' => $chapters
        ]);
    }

    public function docAction(Request $request, $book)
    {
      $docDir      = __DIR__ . "/../Docs";
      $hiddenBooks = [];

      // find all BOOKS (aka dirs)
      $files = new \RecursiveIteratorIterator(
          new \RecursiveDirectoryIterator($docDir),
          \RecursiveIteratorIterator::SELF_FIRST);
      $books = [];
      foreach($files as $file) {
          if (is_dir($file) === true && basename($file) !== '.' && basename($file) !== '..') {
              $bookName      = str_replace($docDir . '/', '', $file);
              $bookName      = str_replace('/', '-', $bookName);
              $allBookName[] = $bookName;
              if (!in_array($bookName, $hiddenBooks)) {
                  $books[] = $bookName;
              }
          }
      }

      // get content choice BOOK
      if ($book && in_array($book, $allBookName)) {
          $book = str_replace('-', '/', $book);
          foreach(glob("$docDir/$book/*.md") as $section) {
              $text      = file_get_contents($section);
              $Parsedown = new Parsedown();
              $content   = $Parsedown->text($text);
              $explodePath = explode('/', $section);
              $filename               = end($explodePath);
              $sectionName            = substr($filename, 0, -3); // remove .md !
              $sections[$sectionName] = $content;
          }
      }

        return $this->render('WshellBundle:Main:doc.html.twig', [
          'active4' => 'active',

          'books'    => json_encode($books),
          'sections' => (isset($sections)) ? $sections : null,
          'book'     => $request->query->get('bookName') ? : null
        ]);
    }
    public function aboutAction()
    {
        return $this->render('WshellBundle:Main:about.html.twig', [
          'active5' => 'active',
        ]);
    }
}
