<!DOCTYPE html>
<html>
    <head>
        <meta charset='UTF-8'>
        <title><?=isset($title)? $title :'Document title'?></title>
        <link rel='stylesheet' type='text/css' href='<?=$baseUrl?>assets/style/style.css'>
    </head>
    <body>
    <header>
        <nav>
            <ul>
                <li><a href='<?=$baseUrl?>'>Home</a></li>
                <li><a href='<?=$baseUrl?>tests/lorem-ipsum'>Tests/lorem-ipsum</a></li>
                <li><a href='<?=$baseUrl?>contact'>Contact us</a></li>
            </ul>
        </nav>
    </header>

        <?=$content?>
    </body>
</html>