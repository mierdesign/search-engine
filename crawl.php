<?php
require '../../simple_html_dom.php';
require_once '../../config/site-config.php'; 
$sql = "SELECT id, site FROM sites";
$result = $link->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
    while($row = $result->fetch_assoc()) {
        $data = $row["site"];
        $urlContent = file_get_contents($data);
        $dom = new DOMDocument();
        @$dom->loadHTML($urlContent);
        $xpath = new DOMXPath($dom);
        $hrefs = $xpath->evaluate("/html/body//a");
        for($i = 0; $i < $hrefs->length; $i++){
          $href = $hrefs->item($i);
          $url = $href->getAttribute('href');
          $url = filter_var($url, FILTER_SANITIZE_URL);
          echo '<a href="'.$url.'">'.$url.'</a><br />';
          $stmt = $link->prepare("INSERT INTO sites (site) VALUES (?)");
          $stmt->bind_param("s", $url);
          $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
            if(preg_match($reg_exUrl, $url)){
              $stmt->execute();
            }else{
              echo "something went wrong";
            }
      }
    }
}else{
  echo "nothing added";
}
function getDescription($url) {
    $tags = get_meta_tags($url);
    return @($tags['description'] ? $tags['description'] : $url);
}
function getMeta($url) {
    $tags = get_meta_tags($url);
    return @($tags['robots'] ? $tags['content'] : $url);
}
$result = mysqli_query($link, "SELECT * FROM sites");
$i=0;
while($row = mysqli_fetch_array($result)) {
$name = $row["site"];
echo $name;
if (empty(file_get_html($name))) {   
        $sql = "DELETE FROM sites WHERE id=".$row["id"];
        echo "Error: no title for". $name;
        if ($link->query($sql) === TRUE) {
            echo "Record deleted successfully";      
        }
    } else {
      if (filter_var($name, FILTER_VALIDATE_URL)) {
            $html = file_get_html($name);
            $title = $html->find('title', 0);
            $title = $title->plaintext;
            echo $title;
            echo "<br>";
            $desc = getDescription($name);
            $meta = getMeta($name);
            var_dump($meta);
            if(strpos($meta , "noindex")){
            echo $site." has noindex";
            }else{
            if(!empty($title)){
            $meta = get_meta_tags($name);
            if(strpos($meta["robots"], "norobots")){
              echo "$name, this url has norobots enabled";
            }else{
            $sql = "INSERT INTO results (url, title, meta) VALUES ('$name', '$title', '$desc')";
            if (mysqli_query($link, $sql)) {
               $sql = "DELETE FROM sites WHERE id=".$row["id"];

               if ($link->query($sql) === TRUE) {
                 echo "Record deleted successfully";
               }else{
                 echo "houston, we have a problem";
               }
            } else {
              echo "Error: " . $sql . "<br>" . mysqli_error($link);
              $sql = "DELETE FROM sites WHERE id=".$row["id"];
              echo "Error: no title for". $name;
              if ($link->query($sql) === TRUE) {
                 echo "Record deleted successfully";
              }
            }
          }
          }else{
              $sql = "DELETE FROM sites WHERE id=".$row["id"];
              echo "Error: no title for". $name;
              if ($link->query($sql) === TRUE) {
                 echo "Record deleted successfully";
            }
        else {
              $sql = "DELETE FROM sites WHERE id=".$row["id"];
              echo "Error: no title for". $name;
              if ($link->query($sql) === TRUE) {
                 echo "Record deleted successfully";      
              }
           }
        }
      }
    }else {
            $sql = "DELETE FROM sites WHERE id=".$row["id"];
            echo "Error: no title for". $name;
            if ($link->query($sql) === TRUE) {
                echo "Record deleted successfully";      
            }
       }
    }
}
   mysqli_close($link);
?>