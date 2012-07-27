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

    private $address = '123 fake street, nowhere town, someplace';

    private $mapUrl = '';

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

    public function getAddress()
    {
        return $this->address;
    }

    public function setMapUrl($url)
    {
        $this->mapUrl = $url;
    }

    public function getMapUrl()
    {
        return $this->mapUrl;
    }
}

class User extends Element
{
    private $images = array();

    private $ip = '127.0.0.1'; // There's no place like 127.0.0.1

    private $latitude = 0;
    private $longitude = 0;

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
        return 1;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
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

    public function visitUser(User $user)
    {
        echo "Visiting function ImageLoader::visitUser(User)\n";

        $userId = $user->getId();
        $images = $this->loadImagesForId($userId);

        $user->setImages($images);
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

class LocationLoader implements Visitor
{

    public function visitProduct(Product $product)
    {
        echo "Visiting function LocationLoader::visitProduct(Product)\n";

        $address = $product->getAddress();
        $location = $this->getLatitudeAndLongitudeByAddress($address);
        $product->setMapUrl(
            'http://maps.google.com/maps?q='
            . $location['lat'] . ','
            . $location['lon']
            . '+(The Product)'
        );
    }

    public function visitUser(User $user)
    {
        echo "Visiting function LocationLoader::visitUser(User)\n";

        $ip = $user->getIp();
        $address = $this->convertIpIntoAddress($ip);
        $location = $this->getLatitudeAndLongitudeByAddress($address);

        $user->setLatitude($location['lat']);
        $user->setLongitude($location['lon']);
    }

    public function defaultVisit(Element $element)
    {
        // error out due to no visitor method being implemented
        $elementClass = get_class($element);
        $thisClass = get_class($this);
        throw new Exception("Visitor method " . $thisClass . "::visit" . $elementClass . "(" . $elementClass . ") is not implemented!");
    }

    private function convertIpIntoAddress($ip)
    {
        return '654 blaville road, eskimo town';
    }

    private function getLatitudeAndLongitudeByAddress($address)
    {
        return array('lat' => 123.98, 'lon' => -234.93);
    }

}

$product = new Product();
$imgLoader = new ImageLoader();
$product->accept($imgLoader);

var_dump($product->getImages());

$user = new User();
$user->accept($imgLoader);

var_dump($user->getImages());
