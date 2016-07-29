public function indexAction()
{
    // retrieve the object from database
    $product = ...;
    if (!$product) {
        throw $this->createNotFoundException('The product does not exist');
    }

    return $this->render(...);
}


// для стандартного исключения возвращается статус 500
throw new \Exception('Something went wrong!');
