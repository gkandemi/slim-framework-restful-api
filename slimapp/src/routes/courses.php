<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

// tüm Kurs listesini getir...
$app->get('/courses', function (Request $request, Response $response) {

    $db = new Db();
    try{
        $db = $db->connect();

        $courses = $db->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_OBJ);

        return $response
            ->withStatus(200)
            ->withHeader("Content-Type", 'application/json')
            ->withJson($courses);

    }catch(PDOException $e){
        return $response->withJson(
            array(
                "error" => array(
                    "text"  => $e->getMessage(),
                    "code"  => $e->getCode()
                )
            )
        );
    }
    $db = null;
});

// kurs detayi..
$app->get('/course/{id}', function (Request $request, Response $response) {

    $id = $request->getAttribute("id");
    $db = new Db();
    try{
        $db = $db->connect();
        $course = $db->query("SELECT * FROM courses WHERE id = $id")->fetch(PDO::FETCH_OBJ);

        return $response
            ->withStatus(200)
            ->withHeader("Content-Type", 'application/json')
            ->withJson($course);

    }catch(PDOException $e){
        return $response->withJson(
            array(
                "error" => array(
                    "text"  => $e->getMessage(),
                    "code"  => $e->getCode()
                )
            )
        );
    }
    $db = null;
});

// yeni kurs ekle...
$app->post('/course/add', function (Request $request, Response $response) {

    $title      = $request->getParam("title");
    $couponCode = $request->getParam("couponCode");
    $price      = $request->getParam("price");

    $db = new Db();
    try{
        $db = $db->connect();
        $statement = "INSERT INTO courses (title,couponCode, price) VALUES(:title, :couponCode, :price)";
        $prepare = $db->prepare($statement);

        $prepare->bindParam("title", $title);
        $prepare->bindParam("couponCode", $couponCode);
        $prepare->bindParam("price", $price);

        $course = $prepare->execute();

        if($course){
            return $response
                ->withStatus(200)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "text"  => "Kurs başarılı bir şekilde eklenmiştir.."
                ));

        } else {
            return $response
                ->withStatus(500)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "error" => array(
                        "text"  => "Ekleme işlemi sırasında bir problem oluştu."
                    )
                ));
        }

    }catch(PDOException $e){
        return $response->withJson(
            array(
                "error" => array(
                    "text"  => $e->getMessage(),
                    "code"  => $e->getCode()
                )
            )
        );
    }
    $db = null;
});

// kurs güncelle..
$app->put('/course/update/{id}', function (Request $request, Response $response) {

    $id = $request->getAttribute("id");

    if($id){

        $title      = $request->getParam("title");
        $couponCode = $request->getParam("couponCode");
        $price      = $request->getParam("price");

        $db = new Db();
        try{
            $db = $db->connect();
            $statement = "UPDATE courses SET title = :title, couponCode = :couponCode, price = :price WHERE id = $id";
            $prepare = $db->prepare($statement);

            $prepare->bindParam("title", $title);
            $prepare->bindParam("couponCode", $couponCode);
            $prepare->bindParam("price", $price);

            $course = $prepare->execute();

            if($course){
                return $response
                    ->withStatus(200)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(array(
                        "text"  => "Kurs başarılı bir şekilde güncellenmiştir.."
                    ));

            } else {
                return $response
                    ->withStatus(500)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(array(
                        "error" => array(
                            "text"  => "Düzenleme işlemi sırasında bir problem oluştu."
                        )
                    ));
            }
        }catch(PDOException $e){
            return $response->withJson(
                array(
                    "error" => array(
                        "text"  => $e->getMessage(),
                        "code"  => $e->getCode()
                    )
                )
            );
        }
        $db = null;
    } else {
        return $response->withStatus(500)->withJson(
            array(
                "error" => array(
                    "text"  => "ID bilgisi eksik.."
                )
            )
        );
    }

});

// kursu sil..
$app->delete('/course/{id}', function (Request $request, Response $response) {

    $id      = $request->getAttribute("id");

    $db = new Db();
    try{
        $db = $db->connect();
        $statement = "DELETE FROM courses WHERE id = :id";
        $prepare = $db->prepare($statement);
        $prepare->bindParam("id", $id);

        $course = $prepare->execute();

        if($course){
            return $response
                ->withStatus(200)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "text"  => "Kurs başarılı bir şekilde silinmiştir.."
                ));

        } else {
            return $response
                ->withStatus(500)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "error" => array(
                        "text"  => "Silme işlemi sırasında bir problem oluştu."
                    )
                ));
        }

    }catch(PDOException $e){
        return $response->withJson(
            array(
                "error" => array(
                    "text"  => $e->getMessage(),
                    "code"  => $e->getCode()
                )
            )
        );
    }
    $db = null;
});
