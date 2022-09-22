<?php

start:
session_start();

header('Content-Type: text/html; charset=utf-8');
echo "
<!DOCTYPE html>
<head>

<meta charset=\"UTF-8\">
<title> Gra w statki </title>
</head>
<body>
";


if (!isset($_SESSION['channel_id']))
{
    $hn = '127.0.0.1:3306';
    $db = 'statki';
    $un = 'gracz';
    $pw = '';
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) die("Błąd połączenia z bazą.");
    $nr = 1;
    while ($nr < 2000)
    {
        $res = $conn->query("SHOW TABLES LIKE 'p" . $nr . "';");
        if ($res->num_rows == 1)
        {
            $res = $conn->query("SELECT filled, filled2 FROM p" . $nr . " WHERE id = 0;");
            if ($res->num_rows == 1)
            {
                $row = $res->fetch_row();
                if ($row[0] == 1 && $row[1] == 1)
                {
                    $nr++;
                    continue;
                }
                else if ($row[0] == 1 && $row[1] == 0)
                {
                    if ($conn->query("UPDATE p" . $nr . " SET filled2 = 1 WHERE id = 0;"))
                    {
                        $_SESSION['channel_id'] = $nr;
                        $_SESSION['player'] = 2;
                        echo <<<_END
                        <div id='game' style="float:left;"></div><div id='ctrl' style="float:right;"></div>
                        <script>
                            var shipsSetted = 0;
                            var isBlocked = 0;
                            var turn = 0;
                            var loh = 0;
                            function asyncRequest()
                            {
                               try
                               {
                                   var request = new XMLHttpRequest()
                               }
                               catch (e1)
                               {
                                   alert(e1.message)
                                   try
                                   {
                                       request = new ActiveXObject("Msxml2.XMLHTTP")
                                   }
                                   catch (e2)
                                   {
                                       try
                                       {
                                           request = new ActiveXObject("Microsoft.XMLHTTP")
                                       }
                                       catch (e3)
                                       {
                                            request = false
                                           alert(e3.message)
                                       }
                                   }
                               }
                               return request
                            }
                            document.getElementById ('game').innerHTML = "<b  id =\"txt1\">   Połączono Cię z graczem. Teraz wybierz gdzie będą Twoje statki. Ustaw 10 statków na polach klikając na nie. Statki nie mogą stać na polach sąsiadujących ze sobą. </b>  "
                                                    
                            if (screen.availWidth < screen.availHeight)
                            {
                                for (let i = 1; i <= 8; i++)
                                {
                                    document.getElementById('game').innerHTML += "<div style=\"padding:0; margin:0; height: " + (0.04 * screen.height) + "px;\" id=" + i + "> </div>"
                                    for (let j = 1; j <= 8; j++)
                                    {
                                        document.getElementById (i).innerHTML += "<img onclick=btclicked(" + i + j + ") id = " + i + j + " src = \"./empty.png\" style=\" height: " + (0.04 * screen.height) + "px; padding: 0; margin: 0; \"  />"
                                    }
                                }
                            }
                            else for (let i = 1; i <= 8; i++)
                            {
                                document.getElementById ('game').innerHTML += "<div style=\"padding:0; margin:0; height: " + (0.06 * screen.availHeight) + "px;\" id=" + i + "> </div>"
                                for (let j = 1; j <= 8; j++)
                                {
                                    document.getElementById (i).innerHTML += "<img onclick=btclicked(" + i + j + ") id = " + i + j + " src = \"./empty.png\" style=\" height: " + 0.06 * (screen.availHeight) + "px; padding: 0; margin: 0; \"  />"
                                }
                            }
                            document.getElementById ('game').innerHTML +=  "<button style=\"background-color: #f4511e;text-align: center; border-radius: 5px; padding: 10px\" onclick=\"submit()\" id=\"submit\"> Zatwierdź </button>"
                                                
                            
                            function btclicked(x)
                            {
                                if (isBlocked != 0) return;
                                if (document.getElementById(x).src.includes("empty.png"))
                                {
                                    if (shipsSetted < 10)
                                    {
                                        if (x < 20)
                                        {
                                            if (x % 10 == 1)
                                            {
                                                if (document.getElementById(x+1).src.includes("ship.png") ||  document.getElementById(x+10).src.includes("ship.png") || document.getElementById(x+11).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                            else if (x % 10 == 8)
                                            {
                                                if (document.getElementById(x-1).src.includes("ship.png") || document.getElementById(x+10).src.includes("ship.png") || document.getElementById(x+9).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                            else
                                            {
                                                if (document.getElementById(x-1).src.includes("ship.png") || document.getElementById(x+1).src.includes("ship.png") ||  document.getElementById(x+10).src.includes("ship.png") || document.getElementById(x+11).src.includes("ship.png") || document.getElementById(x+9).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                        }
                                        else if (x > 80)
                                        {
                                            if (x % 10 == 1)
                                            {
                                                if (document.getElementById(x+1).src.includes("ship.png") || document.getElementById(x-10).src.includes("ship.png") || document.getElementById(x-9).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                            else if (x % 10 == 8)
                                            {
                                                if (document.getElementById(x-1).src.includes("ship.png") || document.getElementById(x-10).src.includes("ship.png") || document.getElementById(x-11).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                            else
                                            {
                                                if (document.getElementById(x-1).src.includes("ship.png") || document.getElementById(x+1).src.includes("ship.png") || document.getElementById(x-10).src.includes("ship.png") || document.getElementById(x-11).src.includes("ship.png") || document.getElementById(x-9).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            if (x % 10 == 1)
                                            {
                                                if (document.getElementById(x+1).src.includes("ship.png") || document.getElementById(x-10).src.includes("ship.png") || document.getElementById(x+10).src.includes("ship.png") || document.getElementById(x+11).src.includes("ship.png") || document.getElementById(x-9).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                            else if (x % 10 == 8)
                                            {
                                                if (document.getElementById(x-1).src.includes("ship.png") || document.getElementById(x-10).src.includes("ship.png") || document.getElementById(x+10).src.includes("ship.png") || document.getElementById(x-11).src.includes("ship.png") || document.getElementById(x+9).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                            else
                                            {
                                                if (document.getElementById(x-1).src.includes("ship.png") || document.getElementById(x+1).src.includes("ship.png") || document.getElementById(x-10).src.includes("ship.png") || document.getElementById(x+10).src.includes("ship.png") || document.getElementById(x-11).src.includes("ship.png") || document.getElementById(x+11).src.includes("ship.png") || document.getElementById(x-9).src.includes("ship.png") || document.getElementById(x+9).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                        }
                                        document.getElementById(x).src = "./ship.png"
                                        shipsSetted++;
                                    }
                                    else alert("Nie możesz ustawić więcej statków niż 10.")
                                }
                                else if (document.getElementById(x).src.includes("ship.png"))
                                {
                                    shipsSetted--;
                                    document.getElementById(x).src = "./empty.png"
                                }
                            }
                            function submit()
                            {
                                scount = 1;
                                if (shipsSetted != 10)
                                {
                                    alert("Nie ustawiłeś wszystkich statków!")
                                    return;
                                }
                                url = "req.php?gameid=$nr&player=2"
                                for (let i = 1; i <= 8; i++)
                                {
                                    for (let j = 1; j <= 8; j++)
                                    {
                                        if (document.getElementById(i.toString() + j.toString()).src.includes("ship.png"))
                                        {
                                            url += "&s" + scount + "=" + ((i-1)*8+j)
                                            scount++
                                        }
                                    }
                                }
                                request = new asyncRequest()
                                request.open("GET", url, true)
                                request.onreadystatechange = function()
                                {
                                    if (this.readyState == 4)
                                    {
                                        if (this.status == 200)
                                        {
                                            if (this.responseText !== null)
                                            {
                                                if (this.responseText.includes("success"))
                                                {
                                                    elem = document.getElementById('submit');
                                                    elem.parentNode.removeChild(elem);
                                                    isBlocked = 1;
                                                    checkShipsReady()
                                                }
                                                else alert("Błąd");
                                            }
                                        }
                                    }
                                }
                                request.send()
                            }
                            function checkShipsReady()
                            {
                                request = asyncRequest()
                                request.open("GET", "req.php?gameid=$nr&player=1&action=shipssetted", true)
                                request.onreadystatechange = function()
                                {
                                    if (this.readyState == 4)
                                    {
                                        if (this.status == 200)
                                        {
                                            if (this.responseText !== null)
                                            {
                                                if (this.responseText.includes("yes"))
                                                {
                                                 elem = document.getElementById('txt1');
                                                    elem.parentNode.removeChild(elem);
                                                    
                                                if (screen.availWidth < screen.availHeight)
                            {
                                for (let i = 1; i <= 8; i++)
                                {
                                    document.getElementById('ctrl').innerHTML += "<div style=\"padding:0; margin:0; height: " + (0.04 * screen.height) + "px;\" id=\"d" + i + "\"> </div>"
                                    for (let j = 1; j <= 8; j++)
                                    {
                                        document.getElementById("d" + i).innerHTML += "<img onclick=btclicked2(" + i + j + ") id = \"d" + i + j + "\" src = \"./empty.png\" style=\" height: " + (0.04 * screen.height) + "px; padding: 0; margin: 0; \"  />"
                                    }
                                }
                            }
                            else for (let i = 1; i <= 8; i++)
                            {
                                document.getElementById ('ctrl').innerHTML += "<div style=\"padding:0; margin:0; height: " + (0.06 * screen.availHeight) + "px;\" id=\"d" + i + "\"> </div>"
                                for (let j = 1; j <= 8; j++)
                                {
                                    document.getElementById ("d" + i).innerHTML += "<img onclick=btclicked2(" + i + j + ") id = \"d" + i + j + "\" src = \"./empty.png\" style=\" height: " + 0.06 * (screen.availHeight) + "px; padding: 0; margin: 0; \"  />"
                                }
                            }
                            document.getElementById ('ctrl').innerHTML += "<br> <b> Plansza do strzelania w statki przeciwnika. </b> <br> <b id=\"kolej\"> Kolejka przeciwnika </b>"
                            
                            document.getElementById ('game').innerHTML += "<br> <b> Plansza strzałów przeciwnika. </b> <br>"
                                                waitForMove()
                                }
                                                else
                                                {
                                                    setTimeout                                  (checkShipsReady
                                                        , 100);
                                                }
                                            }
                                        }
                                    }
                                }
                                request.send()
                            }
                            
                            function btclicked2(x)
                            {
                                if (turn != 1) return;
                                bid = "d" + x;
                                if (!document.getElementById(bid).src.includes("empty.png")) return;
                                fid = (Math.floor(x/10)-1) * 8 + (x % 10)
                                request = new asyncRequest()
                                request.open("GET", "req.php?gameid=$nr&player=2&action=hit&id=" + fid, true)
                                request.onreadystatechange = function()
                                {
                                    if (this.readyState == 4)
                                    {
                                        if (this.status == 200)
                                        {
                                            if (this.responseText !== null)
                                            {
                                                if (this.responseText.includes("missed"))
                                                {
                                                    document.getElementById(bid).src = "./missed.png"
                                                }
                                                else if (this.responseText.includes("hit"))
                                                {
                                                    document.getElementById(bid).src = "./hit.png"
                                                    if (document.getElementById("d"+ (x-11)) !== null) document.getElementById("d"+ (x-11)).src = "./missed.png"
                                                    if (document.getElementById("d"+ (x-10)) !== null) document.getElementById("d"+ (x-10)).src = "./missed.png"
                                                    if (document.getElementById("d"+ (x-9)) !== null)document.getElementById("d"+ (x-9)).src = "./missed.png"
                                                    if (document.getElementById("d"+ (x-1)) !== null)document.getElementById("d"+ (x-1)).src = "./missed.png"
                                                    if (document.getElementById("d"+ (x+1)) !== null)document.getElementById("d"+ (x+1)).src = "./missed.png"
                                                    if (document.getElementById("d"+ (x+9)) !== null)document.getElementById("d"+ (x+9)).src = "./missed.png"
                                                    if (document.getElementById("d"+ (x+10)) !== null)document.getElementById("d"+ (x+10)).src = "./missed.png"
                                                    if (document.getElementById("d"+ (x+11)) !== null)document.getElementById("d"+ (x+11)).src = "./missed.png"
                                                }
                                                else if (this.responseText.includes("win"))
                                                {
                                                    alert("Brawo! Wygrałeś!")
                                                    document.getElementById('game').innerHTML = ""
                                                    document.getElementById('ctrl').innerHTML = ""
                                                    request = new asyncRequest()
                                request.open("GET", "req.php?reset=1", false)
                                                    request.send()
                                                }
                                                turn = 0
                                                document.getElementById('kolej').innerText = "Kolej przeciwnika"
                                                waitForMove()
                                            }
                                        }
                                    }
                                }
                                request.send()
                            }
                            function waitForMove()
                            {
                                request = new asyncRequest()
                                request.open("GET", "req.php?action=lastophit&gameid=$nr&player=2", true)
                                request.onreadystatechange = function()
                                {
                                    if (this.readyState == 4)
                                    {
                                        if (this.status == 200)
                                        {
                                            if (this.responseText !== null)
                                            {
                                                if (this.responseText.includes(loh) || isNaN(parseInt(this.responseText)))
                                                {
                                                    setTimeout                                  (waitForMove
                                                        , 100);
                                                }
                                                else
                                                {
                                                    loh = parseInt(this.responseText)
                                                    turn = 1
                                                    document.getElementById('kolej').innerText = "Twoja kolej"
                                                    n1 = Math.floor(loh / 8) + 1
                                                    n2 = loh % 8
                                                    if (n2 == 0)
                                                    {
                                                        n1 -= 1
                                                        n2 = 8
                                                    }
                                                    bid = n1.toString() + n2.toString()
                                                    if (document.getElementById(bid).src.includes("ship.png"))
                                                    {
                                                        document.getElementById(bid).src = "./hit.png"
                                                    }
                                                    else if (document.getElementById(bid).src.includes("empty.png"))
                                                    {
                                                        document.getElementById(bid).src = "./missed.png"
                                                    }
                                                    if (!document.getElementById('game').innerHTML.includes("ship.png"))
                                                    {
                                                    alert("No niestety, przegrałeś.")
                                                        document.getElementById('game').innerHTML = ""
                                                    document.getElementById('ctrl').innerHTML = ""
                                                    request = new asyncRequest()
                                request.open("GET", "req.php?reset=1", false)
                                                    request.send()
                                                    return;
                                                    }
                                                }
                                            }
                                            
                                        }
                                    }
                                }
                                request.send()
                            }
                        </script>
_END;
                        break;
                    }
                    else die("Błąd1");
                }
            }
            
        }
        else // Tabela nie istnieje i można ją stworzyć
            {
                if ($conn->query("CREATE TABLE p" . $nr . " (id INT, filled INT, hit INT, filled2 INT, hit2 INT);"))
                {
                    if (!$conn->query("INSERT INTO p" . $nr . " (id, filled, hit, hit2, filled2) VALUES (0, 1, 0, 0, 0);")) die("Błąd2");
                    for ($i = 1; $i <= 64; $i++)
                    {
                        if (!$conn->query("INSERT INTO p" . $nr . " (id, filled, hit, filled2, hit2) VALUES (" . $i . ", 0, 0, 0, 0);")) die("Błąd3");
                    }
                    $_SESSION['channel_id'] = $nr;
                    $_SESSION['player'] = 1;
                    echo <<<_END
                        <div id='game' style="float:left;"> <b> Oczekiwanie na drugiego gracza... </b> </div><div id='ctrl' style="float:right;"></div>
                        <script>
                            var shipsSetted = 0;
                            var isBlocked = 0;
                            var turn = 1;
                            var loh = 0;
                            function asyncRequest()
                            {
                               try
                               {
                                   var request = new XMLHttpRequest()
                               }
                               catch (e1)
                               {
                                   alert(e1.message)
                                   try
                                   {
                                       request = new ActiveXObject("Msxml2.XMLHTTP")
                                   }
                                   catch (e2)
                                   {
                                       try
                                       {
                                           request = new ActiveXObject("Microsoft.XMLHTTP")
                                       }
                                       catch (e3)
                                       {
                                            request = false
                                           alert(e3.message)
                                       }
                                   }
                               }
                               return request
                            }
                            function checkForSecondPlayer() {
                                request = new asyncRequest()
                                request.open("GET", "req.php?gameid=$nr&action=wait", true)
                                request.onreadystatechange = function()
                                {
                                    if (this.readyState == 4)
                                    {
                                        if (this.status == 200)
                                        {
                                            if (this.responseText !== null)
                                            {
                                                
                                                if (this.responseText.includes( "code1"))
                                                {
                                                    
                                                    document.getElementById                 ('game').innerHTML = "<b id =\"txt1\">               Drugi gracz połączył się. Teraz wybierz gdzie będą Twoje statki. Ustaw 10 statków na polach klikając na nie. Statki nie mogą stać na polach sąsiadujących ze sobą. </b> "
                                                    
                                                    if (screen.availWidth < screen.availHeight)
                                                    for (let i = 1; i <= 8; i++)             {
                                                    document.getElementById ('game').innerHTML += "<div style=\"padding:0; margin:0; height: " + (0.04 * screen.height) + "px;\" id=" + i + "> </div>"
                                                        for (let j = 1; j <= 8; j++)            {
            document.getElementById (i).innerHTML += "<img onclick=btclicked(" + i + j + ") id = " + i + j + " src = \"./empty.png\" style=\" height: " + (0.04 * screen.height) + "px; padding: 0; margin: 0; \"  />"
                                                        }
                                                    }
                                                    
                                                    else for (let i = 1; i <= 8; i++)             {
                                                    document.getElementById ('game').innerHTML += "<div style=\"padding:0; margin:0; height: " + (0.06 * screen.availHeight) + "px;\" id=" + i + "> </div>"
                                                        for (let j = 1; j <= 8; j++)            {
            document.getElementById (i).innerHTML += "<img onclick=btclicked(" + i + j + ") id = " + i + j + " src = \"./empty.png\" style=\" height: " + 0.06 * (screen.availHeight) + "px; padding: 0; margin: 0; \"  />"
                                                        }
                                                        
                                                    }
                                                    
                            document.getElementById ('game').innerHTML +=  "<button style=\"background-color: #f4511e;text-align: center; border-radius: 5px; padding: 10px\" onclick=\"submit()\" id=\"submit\"> Zatwierdź </button>"
                                                }
                                                else
                                                {
                                                    console.log(this.responseText)
                                                    setTimeout                                  (checkForSecondPlayer
                                                        , 100);
                                                        
                                                }
                                            }
                                        }
                                    }
                                    
                                }
                                request.send()
                            }
                            checkForSecondPlayer()
                            
                            function btclicked(x)
                            {
                                if (isBlocked != 0) return;
                                if (document.getElementById(x).src.includes("empty.png"))
                                {
                                    if (shipsSetted < 10)
                                    {
                                        if (x < 20)
                                        {
                                            if (x % 10 == 1)
                                            {
                                                if (document.getElementById(x+1).src.includes("ship.png") ||  document.getElementById(x+10).src.includes("ship.png") || document.getElementById(x+11).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                            else if (x % 10 == 8)
                                            {
                                                if (document.getElementById(x-1).src.includes("ship.png") || document.getElementById(x+10).src.includes("ship.png") || document.getElementById(x+9).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                            else
                                            {
                                                if (document.getElementById(x-1).src.includes("ship.png") || document.getElementById(x+1).src.includes("ship.png") ||  document.getElementById(x+10).src.includes("ship.png") || document.getElementById(x+11).src.includes("ship.png") || document.getElementById(x+9).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                        }
                                        else if (x > 80)
                                        {
                                            if (x % 10 == 1)
                                            {
                                                if (document.getElementById(x+1).src.includes("ship.png") || document.getElementById(x-10).src.includes("ship.png") || document.getElementById(x-9).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                            else if (x % 10 == 8)
                                            {
                                                if (document.getElementById(x-1).src.includes("ship.png") || document.getElementById(x-10).src.includes("ship.png") || document.getElementById(x-11).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                            else
                                            {
                                                if (document.getElementById(x-1).src.includes("ship.png") || document.getElementById(x+1).src.includes("ship.png") || document.getElementById(x-10).src.includes("ship.png") || document.getElementById(x-11).src.includes("ship.png") || document.getElementById(x-9).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            if (x % 10 == 1)
                                            {
                                                if (document.getElementById(x+1).src.includes("ship.png") || document.getElementById(x-10).src.includes("ship.png") || document.getElementById(x+10).src.includes("ship.png") || document.getElementById(x+11).src.includes("ship.png") || document.getElementById(x-9).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                            else if (x % 10 == 8)
                                            {
                                                if (document.getElementById(x-1).src.includes("ship.png") || document.getElementById(x-10).src.includes("ship.png") || document.getElementById(x+10).src.includes("ship.png") || document.getElementById(x-11).src.includes("ship.png") || document.getElementById(x+9).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                            else
                                            {
                                                if (document.getElementById(x-1).src.includes("ship.png") || document.getElementById(x+1).src.includes("ship.png") || document.getElementById(x-10).src.includes("ship.png") || document.getElementById(x+10).src.includes("ship.png") || document.getElementById(x-11).src.includes("ship.png") || document.getElementById(x+11).src.includes("ship.png") || document.getElementById(x-9).src.includes("ship.png") || document.getElementById(x+9).src.includes("ship.png"))
                                                {
                                                    alert("Statki nie mogą sąsiadować!");
                                                    return;
                                                }
                                            }
                                        }
                                        document.getElementById(x).src = "./ship.png"
                                        shipsSetted++;
                                    }
                                    else alert("Nie możesz ustawić więcej statków niż 10.")
                                }
                                else if (document.getElementById(x).src.includes("ship.png"))
                                {
                                    shipsSetted--;
                                    document.getElementById(x).src = "./empty.png"
                                }
                            }
                            function submit()
                            {
                                scount = 1;
                                if (shipsSetted != 10)
                                {
                                    alert("Nie ustawiłeś wszystkich statków!")
                                    return;
                                }
                                url = "req.php?gameid=$nr&player=1"
                                for (let i = 1; i <= 8; i++)
                                {
                                    for (let j = 1; j <= 8; j++)
                                    {
                                        if (document.getElementById(i.toString() + j.toString()).src.includes("ship.png"))
                                        {
                                            url += "&s" + scount + "=" + ((i-1)*8+j)
                                            scount++
                                        }
                                    }
                                }
                                request = new asyncRequest()
                                request.open("GET", url, true)
                                request.onreadystatechange = function()
                                {
                                    if (this.readyState == 4)
                                    {
                                        if (this.status == 200)
                                        {
                                            if (this.responseText !== null)
                                            {
                                                if (this.responseText.includes("success"))
                                                {
                                                    elem = document.getElementById('submit');
                                                    elem.parentNode.removeChild(elem);
                                                    isBlocked = 1;
                                                    checkShipsReady()
                                                }
                                                else alert("Błąd");
                                            }
                                        }
                                    }
                                }
                                request.send()
                            }
                            function checkShipsReady()
                            {
                                request = asyncRequest()
                                request.open("GET", "req.php?gameid=$nr&player=2&action=shipssetted", true)
                                request.onreadystatechange = function()
                                {
                                    if (this.readyState == 4)
                                    {
                                        if (this.status == 200)
                                        {
                                            if (this.responseText !== null)
                                            {
                                                if (this.responseText.includes("yes"))
                                                {
                                                elem = document.getElementById('txt1');
                                                    elem.parentNode.removeChild(elem);
                            if (screen.availWidth < screen.availHeight)
                            {
                                for (let i = 1; i <= 8; i++)
                                {
                                    document.getElementById('ctrl').innerHTML += "<div style=\"padding:0; margin:0; height: " + (0.04 * screen.height) + "px;\" id=\"d" + i + "\"> </div>"
                                    for (let j = 1; j <= 8; j++)
                                    {
                                        document.getElementById("d" + i).innerHTML += "<img onclick=btclicked2(" + i + j + ") id = \"d" + i + j + "\" src = \"./empty.png\" style=\" height: " + (0.04 * screen.height) + "px; padding: 0; margin: 0; \"  />"
                                    }
                                }
                            }
                            else for (let i = 1; i <= 8; i++)
                            {
                                document.getElementById ('ctrl').innerHTML += "<div style=\"padding:0; margin:0; height: " + (0.06 * screen.availHeight) + "px;\" id=\"d" + i + "\"> </div>"
                                for (let j = 1; j <= 8; j++)
                                {
                                    document.getElementById ("d" + i).innerHTML += "<img onclick=btclicked2(" + i + j + ") id = \"d" + i + j + "\" src = \"./empty.png\" style=\" height: " + 0.06 * (screen.availHeight) + "px; padding: 0; margin: 0; \"  />"
                                }
                            }
                            document.getElementById ('ctrl').innerHTML += "<br> <b> Plansza do strzelania w statki przeciwnika. </b> <br> <b id=\"kolej\"> Twoja kolej </b>"
                            
                            document.getElementById ('game').innerHTML += "<br> <b> Plansza strzałów przeciwnika. </b> <br>"
                                                }
                                                else
                                                {
                                                    setTimeout                                  (checkShipsReady
                                                        , 100);
                                                }
                                            }
                                        }
                                    }
                                }
                                request.send()
                            }
                            function btclicked2(x)
                            {
                                if (turn != 1) return;
                                bid = "d" + x;
                                if (!document.getElementById(bid).src.includes("empty.png")) return;
                                fid = (Math.floor(x/10)-1) * 8 + (x % 10)
                                request = new asyncRequest()
                                request.open("GET", "req.php?gameid=$nr&player=1&action=hit&id=" + fid, true)
                                request.onreadystatechange = function()
                                {
                                    if (this.readyState == 4)
                                    {
                                        if (this.status == 200)
                                        {
                                            if (this.responseText !== null)
                                            {
                                                if (this.responseText.includes("missed"))
                                                {
                                                    document.getElementById(bid).src = "./missed.png"
                                                }
                                                else if (this.responseText.includes("hit"))
                                                {
                                                    document.getElementById(bid).src = "./hit.png"
                                                    if (document.getElementById("d"+ (x-11)) !== null) document.getElementById("d"+ (x-11)).src = "./missed.png"
                                                    if (document.getElementById("d"+ (x-10)) !== null) document.getElementById("d"+ (x-10)).src = "./missed.png"
                                                    if (document.getElementById("d"+ (x-9)) !== null)document.getElementById("d"+ (x-9)).src = "./missed.png"
                                                    if (document.getElementById("d"+ (x-1)) !== null)document.getElementById("d"+ (x-1)).src = "./missed.png"
                                                    if (document.getElementById("d"+ (x+1)) !== null)document.getElementById("d"+ (x+1)).src = "./missed.png"
                                                    if (document.getElementById("d"+ (x+9)) !== null)document.getElementById("d"+ (x+9)).src = "./missed.png"
                                                    if (document.getElementById("d"+ (x+10)) !== null)document.getElementById("d"+ (x+10)).src = "./missed.png"
                                                    if (document.getElementById("d"+ (x+11)) !== null)document.getElementById("d"+ (x+11)).src = "./missed.png"
                                                }
                                                else if (this.responseText.includes("win"))
                                                {
                                                    alert("Brawo! Wygrałeś!")
                                                    document.getElementById('game').innerHTML = ""
                                                    document.getElementById('ctrl').innerHTML = ""
                                                    request = new asyncRequest()
                                request.open("GET", "req.php?reset=1", false)
                                                    request.send()
                                                    return;
                                                }
                                                turn = 0
                                                document.getElementById('kolej').innerText = "Kolej przeciwnika"
                                                waitForMove()
                                            }
                                        }
                                    }
                                }
                                request.send()
                            }
                            function waitForMove()
                            {
                                request = new asyncRequest()
                                request.open("GET", "req.php?action=lastophit&gameid=$nr&player=1", true)
                                request.onreadystatechange = function()
                                {
                                    if (this.readyState == 4)
                                    {
                                        if (this.status == 200)
                                        {
                                            if (this.responseText !== null)
                                            {
                                                if (this.responseText.includes(loh)  || isNaN(parseInt(this.responseText)))
                                                {
                                                    setTimeout                                  (waitForMove
                                                        , 100);
                                                }
                                                else
                                                {
                                                    loh = parseInt(this.responseText)
                                                    turn = 1
                                                    document.getElementById('kolej').innerText = "Twoja kolej"
                                                    n1 = Math.floor(loh / 8) + 1
                                                    n2 = loh % 8
                                                    if (n2 == 0)
                                                    {
                                                        n1 -= 1
                                                        n2 = 8
                                                    }
                                                    bid = n1.toString() + n2.toString()
                                                    if (document.getElementById(bid).src.includes("ship.png"))
                                                    {
                                                        document.getElementById(bid).src = "./hit.png"
                                                    }
                                                    else if (document.getElementById(bid).src.includes("empty.png"))
                                                    {
                                                        document.getElementById(bid).src = "./missed.png"
                                                    }
                                                    if (!document.getElementById('game').innerHTML.includes("ship.png"))
                                                    {
                                                    alert("No niestety, przegrałeś.")
                                                        document.getElementById('game').innerHTML = ""
                                                    document.getElementById('ctrl').innerHTML = ""
                                                    request = new asyncRequest()
                                request.open("GET", "req.php?reset=1", false)
                                                    request.send()
                                                    return;
                                                    }
                                                }
                                            }
                                            
                                        }
                                    }
                                }
                                request.send()
                            }
                        </script>
_END;
                    break;
                }
            }
    }
}
else
{
    session_destroy();
    goto start; // Powrót przy odświeżaniu
}


echo "</body>
</html>
";




?>