<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Publications</title>
	<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body>
<div class="main">
<h1>Publication list</h1>  
  <table width="100%" border="0">
  <form action="p_search.php" method="get">
  <?if (($_GET["project"] != "")){?>
  <tr><td width="60"><i>Project:</i></td><td><strong><?=strtoupper($_GET["project"])?></strong></td></tr>
  <ul>
  <?}?>
  <tr><td width="60"><i>Author:</i></td><td> <input type="text" name="author" value="<?=$_GET["author"]?>"></td></tr>
  <tr><td width="60"><i>Title:</i> </td><td><input type="text" name="title" value="<?=$_GET["title"]?>"></td></tr>
  <tr><td width="60"><i>Year:</i> </td><td><input type="text" name="year" value="<?=$_GET["year"]?>"></td></tr>
  </tr><td></td><td><input type="submit" value="Search"></td></tr>
  </form>
  </table>
  <ul>
<?

$file = implode('',file('../pubs/OURS.bib'));
$lines = explode("@",$file);
foreach ($lines as $line_num => $line) {
		//echo $line."<br><br>";
		$ent = explode("\n",$line);
		foreach ($ent as $ent_num => $field) {
			if ($ent_num == 0) {
				$id = explode('{',$field);
				$id = explode(',',$id[1]);
				$id = $id[0];
			}
		}
		if ($id != "") {
			$bib[$id]='@'.$line;
			$bibData[$id] = anoBib($id, $bib);
			$idGravar = implode('',explode("+",implode('',explode("&",$id))));
			//$bibGravar[$idGravar] = '@'.$line;
		}
}

//gravarArquivo("OURS_novo.bib", $bibGravar);

arsort($bibData);

if ($_GET["project"] != ""){
	mostrarProjeto($bib,$bibData,$_GET["project"]);
}
elseif ($_GET["action"] == "bib") {
	echo "<p align=\"right\"><a href=\"javascript: history.go(-1)\"><b>Back</b></a></p>";
	//str_replace("+"," ",$_GET["id"]));
	if (strpos($_GET["id"],"|")) mostrarBib(str_replace("|","&",$_GET["id"]), $bib);
	else mostrarBib(urlencode($_GET["id"]), $bib);
}
elseif (($_GET["author"] == "" and $_GET["title"] == "" and $_GET["year"] == "")) {
	mostrarTodos($bib,$bibData);
}
else {
	mostrarFiltrado($_GET["author"],$_GET["title"],$_GET["year"],$bib,$bibData);
}


function mostrarBib($id, $bib){
	//$bibData
	echo "<hr><br>";
	$e = explode("\n",$bib[$id]);
	echo implode("<br>",$e);
	echo "<hr>";
}

/*function mostrarTodos($bib){
	//ksort($bib);
	$ultimoAno = "";
	foreach ($bib as $id => $ent) {
		$ano = anoBib($id, $bib);
		if ($ultimoAno != $ano) {
			echo "<h2>".$ano."</h2><br>";
			$ultimoAno = $ano;
		}
		mostrarBibHtml($id, $bib);
	}
}*/

function mostrarProjeto($bib,$bibData,$projeto){
	$file = implode('',file('../pubs/'.$projeto.'.cite'));
	//echo $file;
	$lines = explode(",",$file);
	foreach ($lines as $id => $key) {
		mostrarBibHtml(trim($key), $bib,$bibData);
	}
}

function mostrarTodos($bib,$bibData){
	$ultimoAno = "";
	foreach ($bibData as $id => $ent) {
		$ano = anoBib($id, $bib);
		if ($ultimoAno != $ano) {
			echo "<h2>".$ano."</h2><br>";
			$ultimoAno = $ano;
		}
		mostrarBibHtml($id, $bib,$bibData);
	}
}

function mostrarFiltrado($author,$title,$year,$bib,$bibData){
	$ultimoAno = "";
	foreach ($bibData as $id => $ent) {
		$ano = anoBib($id, $bib);
		//echo $title. " ".tituloBib($id, $bib)." = ".strpos(tituloBib($id, $bib),$title). "<br>";
		if (!$year or $year == $ano) {
			if ($author and strpos(" ".strtoupper(autorBib($id, $bib)),strtoupper($author)) and !$title) {
				if ($ultimoAno != $ano) {
					echo "<h2>".$ano."</h2><br>";
					$ultimoAno = $ano;
				}
				mostrarBibHtml($id, $bib,$bibData);
			}
			elseif ($title and strpos(" ".strtoupper(tituloBib($id, $bib)),strtoupper($title)) and !$author) {
				if ($ultimoAno != $ano) {
					echo "<h2>".$ano."</h2><br>";
					$ultimoAno = $ano;
				}
				mostrarBibHtml($id, $bib,$bibData);
			}
			elseif ($title and $author and strpos(" ".strtoupper(tituloBib($id, $bib)),strtoupper($title)) and strpos(" ".strtoupper(autorBib($id, $bib)),strtoupper($author))) {
				if ($ultimoAno != $ano) {
					echo "<h2>".$ano."</h2><br>";
					$ultimoAno = $ano;
				}
				mostrarBibHtml($id, $bib,$bibData);
			}
			elseif (!$title and !$author) {
				if ($ultimoAno != $ano) {
					echo "<h2>".$ano."</h2><br>";
					$ultimoAno = $ano;
				}
				mostrarBibHtml($id, $bib,$bibData);
			}
		}
		
	}
}

function anoBib($id, $bib){
	$ent = explode("\n",$bib[$id]);
	foreach ($ent as $ent_num => $field) {
			$f = explode("=",$field);
			if (substr($f[1],-1) == ",") $f[1] = substr($f[1],0,-1);
			if (trim($f[0]) == "year") {
				//echo $f[1];
				return (supertrim($f[1]));
			}
	}
}

function autorBib($id, $bib){
	$ent = explode("\n",$bib[$id]);
	foreach ($ent as $ent_num => $field) {
			$f = explode("=",$field);
			if (substr($f[1],-1) == ",") $f[1] = substr($f[1],0,-1);
			if (trim($f[0]) == "author") {
				//echo $f[1];
				return (supertrim($f[1]));
			}
	}
}

function tituloBib($id, $bib){
	$ent = explode("\n",$bib[$id]);
	foreach ($ent as $ent_num => $field) {
			$f = explode("=",$field);
			if (substr($f[1],-1) == ",") $f[1] = substr($f[1],0,-1);
			if (trim($f[0]) == "title") {
				//echo $f[1];
				return (supertrim($f[1]));
			}
	}
}

function mostrarBibHtml($id, $bib, $bibData){
	//echo $id." - ".$bibData[$id]."<br>";
	$volume = "";
	$number = "";
	$month = "";
	$url = "";
	$ent = explode("\n",$bib[$id]);
	foreach ($ent as $ent_num => $field) {
		if ($ent_num != 0) {
			//echo $field."<br>";
			$f = explode("=",$field);
			//echo "|".$f[0]."|<br>";
			if (substr($f[1],-1) == ",") $f[1] = substr(trim($f[1]),0,-1);
			if (trim($f[0]) == "author") $author = supertrim($f[1]);
			if (trim($f[0]) == "year") $year = ", ".supertrim($f[1]);
			if (trim($f[0]) == "title") $title = ". ".supertrim(replaceSpecialCharacteres($f[1]));

			if (trim($f[0]) == "url"){
        	                $temp_url = explode("://",supertrim($f[1]));
	                        //echo "<br><h1>".$temp_url[0]."</h1><br>";
                                
                        	if ($temp_url[0] == "http") $url = "<a href=\"".supertrim($f[1])."\"><b>[FILE]</b></a>";
                	        else {
					$url = "<a href=\"http://".supertrim($f[1])."\"><b>[FILE]</b></a>";
					//echo "<br> $url <br>";
				}
			}
//echo $url."<br>";
			if (trim($f[0]) == "booktitle" or trim($f[0]) == "journal") $journal = ". ".supertrim($f[1]);
			if (trim($f[0]) == "volume") $volume = ". vol.".supertrim($f[1]);
			if (trim($f[0]) == "number") {
				$number = substr($f[1],1,-1);
				while (substr($number,0,1) == "{") $number = substr($number,1,-1);
				$number = ". ".implode("-",explode("--",supertrim2($number)));
			}
			if (trim($f[0]) == "month")  $month = ". ".supertrim($f[1]);
			if (trim($f[0]) == "pages")  {
				$pages = substr($f[1],1,-1);
				while (substr($pages,0,1) == "{") $pages = substr($pages,1,-1);
				$pages = ". ".implode("-",explode("--",supertrim2($pages)));
			}
		}
	}
	echo "<li>".replaceSpecialCharacteres($author).$year."<b><i>".$title."</b></i>".replaceSpecialCharacteres($journal).$volume.$number.$month.$pages.". ".replaceSpecialCharacteres($url)." <a href=\"p_search.php?action=bib&id=".str_replace("&","|",$id)."\"><b>[BIB]</b></a></li><br>";
	//echo $url."<br>";
}


?>
</ul>
<p>&nbsp;</p>  
</div>
</body>
</html>
<?

function supertrim($temp){
$temp = trim($temp);
$temp = substr($temp,1,-1);
$temp = implode("",explode("}",implode("",explode("{",$temp))));
$temp = implode("",explode("\t",$temp));
return $temp;
}

function supertrim2($temp){
$temp = trim($temp);
$temp = implode("",explode("\t",$temp));
return $temp;
}

function replaceSpecialCharacteres($temp) {
        $temp = str_replace("\\'A","Á",$temp);
        $temp = str_replace("\\'a","á",$temp);
        $temp = str_replace("\\^A","Â",$temp);
        $temp = str_replace("\\^a","â",$temp);
        $temp = str_replace("\\\"a","ä",$temp);
        $temp = str_replace("\\'E","É",$temp);
        $temp = str_replace("\\'e","é",$temp);
	$temp = str_replace("\\^E","Ê",$temp);
        $temp = str_replace("\\^e","ê",$temp);
        $temp = str_replace("\\'I","Í",$temp);
        $temp = str_replace("\\'i","í",$temp);
        $temp = str_replace("\\'O","Ó",$temp);
        $temp = str_replace("\\'o","ó",$temp);
        $temp = str_replace("\\^O","Ô",$temp);
        $temp = str_replace("\\^o","ô",$temp);
        $temp = str_replace("\\\"O","Ö",$temp);
        $temp = str_replace("\\\"o","ö",$temp);
        $temp = str_replace("\\'U","Ú",$temp);
        $temp = str_replace("\\'u","ú",$temp);
        $temp = str_replace("\\\"U","Ü",$temp);
        $temp = str_replace("\\\"u","ü",$temp);
        $temp = str_replace("\\'C","Ç",$temp);
        $temp = str_replace("\\'c","ç",$temp);
	$temp = str_replace("\\cC","Ç",$temp);
        $temp = str_replace("\\cc","ç",$temp);
	$temp = str_replace("\\c C","Ç",$temp);
        $temp = str_replace("\\c c","ç",$temp);
        $temp = str_replace("\\~A","Ã",$temp);
        $temp = str_replace("\\~a","ã",$temp);
        $temp = str_replace("&","&amp;",$temp);
	$temp = str_replace("'","&apos;",$temp);
        return $temp;
}


/*
function gravarArquivo($nomeArquivo, $bibGravar) {
		if (!$arquivo = fopen($nomeArquivo, 'a')) {
	         echo "Erro abrindo arquivo ($nomeArquivo)";
	         exit;
	 	}
		ksort($bibGravar);
		foreach ($bibGravar as $id => $ent) {
			if (!fwrite($arquivo, $ent."\n")) {
				echo "Erro escrevendo no arquivo $nomeArquivo.";
	       		exit;
			}
		}
	   	echo "O arquivo $nomeArquivo foi escrito com sucesso.";
	   	fclose($arquivo);
}
*/

?>
