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
}

class ImageLoader implements Visitor
{

    public function visitProduct(Product $product)
    {
        echo "Visiting function ImageLoader::visitProduct(Product)\n";
    }

    public function defaultVisit(Element $element)
    {
        // error out due to no visitor method being implemented
        $elementClass = get_class($element);
        $thisClass = get_class($this);
        throw new Exception("Visitor method " . $thisClass . "::visit" . $elementClass . "(" . $elementClass . ") is not implemented!");
    }

}

$product = new Product();
$imgLoader = new ImageLoader();
$product->accept($imgLoader);
