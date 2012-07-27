<?php

abstract class Element
{

    public function accept(Visitor $visitor)
    {

        // ... Call visitFoo, etc, here

        $visitMethods = get_class_methods($visitor); $elementClass = get_class($this);

        foreach ($visitMethods as $method) {

            // we've found the visitation method for this class type
            if ('visit' . $elementClass == $method) {

                // visit the method and exit
                $visitor->{'visit' . $elementClass}($this);
                return;
            }
        }

        // If no visitFoo, etc, call a default algorithm
        $visitor->defaultVisit($this);

    }

}

interface Visitor
{
    public function defaultVisit(Element $element);
}

class Product extends Element
{
    private $images = array();

    public function setImages($images)
    {
        $this->images = $images;
    }

    public function getImages()
    {
        return $this->images;
    }

    public function getId()
    {
        return 1; // Magic number FTW!
    }
}

class ImageLoader implements Visitor
{

    public function visitProduct(Product $product)
    {
        echo "Visiting function ImageLoader::visitProduct(Product)\n";

        $productId = $product->getId();
        $images = $this->loadImagesForId($productId);

        $product->setImages($images);
    }

    public function defaultVisit(Element $element)
    {
        // error out due to no visitor method being implemented
        $elementClass = get_class($element);
        $thisClass = get_class($this);
        throw new Exception("Visitor method " . $thisClass . "::visit" . $elementClass . "(" . $elementClass . ") is not implemented!");
    }

    private function loadImagesForId($id)
    {
        // Some sort of generic load functionality based on an id
        // Could just as easily be an SQL clause, or a path name, etc
        // The idea is that the algorithm for loading images should be
        // the same no matter what it is being loaded for (more on this
        // later)

        // Return dummy data for this example
        return array(
            '/my/img/harbourbridge.png',
            '/my/img/water.jpg',
            '/my/img/phone.tiff'
        );
    }
}

$product = new Product();
$imgLoader = new ImageLoader();
$product->accept($imgLoader);

var_dump($product->getImages());
