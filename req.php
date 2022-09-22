<?php
// Skrypt obsługujący żądania wysyłane asynchronicznie przez aplikację.

session_start(); // Zaczęcie sesji


// Poniżej dane do logowania do bazy danych
$db = 'statki'; // Nazwa naszej bazy danych
$un = 'gracz'; // Użytkownik (login)
$pw = ''; // Hasło użytkownika
$hn = '127.0.0.1:3306'; // IP i port bazy danych

// ISTOTNE! Kiedy będzie Pan konfigurował użytkownika bazy danych, należy dać mu uprawnienia do wszystkich komend (szczególnie CREATE, która domyślnie jest niedozwolona)

$conn = new mysqli($hn, $un, $pw, $db); // Tutaj łączymy się z bazą danych
if ($conn->connect_error) die("base_error"); // Błąd połączenia z bazą danych

if (isset($_GET['action']) && $_GET['action'] === "wait" && isset($_GET['gameid'])) 
{
    // Akcja wait:
    // Argumenty: action - musi być ustawiony na 'wait'
    // gameid - numer gry (nr planszy w bazie danych)
    $gameid = $conn->real_escape_string($_GET['gameid']);
    $res = $conn->query("SELECT filled2 FROM p$gameid WHERE id = 0;");
    if ($res->num_rows == 1)
    {
        $row = $res->fetch_row();
        if ($row[0] == 1)
            echo "code1";
        else echo "code0";
    }
    else die("code0");
    exit;
}

if (isset($_GET['gameid']) && isset($_GET['player']) && isset($_GET['s1']))
{
    // Argumenty: s1,s2,...,s10 - są to pozycje statków które chce ustawić
    // gameid - numer gry (nr planszy w bazie danych)
    // player - 1 lub 2 zależnie od tego który gracz wysyła pozycje swoich statków
    $gameid = $conn->real_escape_string($_GET['gameid']);
    $pl = $conn->real_escape_string($_GET['player']);
    if ($pl == 1)
        for ($i = 1; $i <= 10; $i++)
        {
            $x = 's' . $i;
            $s = $conn->real_escape_string($_GET[$x]);
            if (!$conn->query("UPDATE p$gameid SET filled=1 WHERE id=$s;"))
                die("error");
        }
    else
        for ($i = 1; $i <= 10; $i++)
        {
            $x = 's' . $i;
            $s = $conn->real_escape_string($_GET[$x]);
            if (!$conn->query("UPDATE p$gameid SET filled2=1 WHERE id=$s;"))
                die("error");
        }
    echo "success";
}

if (isset($_GET['action']) && $_GET['action'] === "shipssetted" && isset($_GET['player']) && isset($_GET['gameid']))
{
    // Akcja shipssetted:
    // Argumenty: action - musi być ustawiony na 'shipssetted'
    // gameid - numer gry (nr planszy w bazie danych)
    // player - 1 lub 2 zależnie od tego który gracz wysyła pozycje swoich statków
    // Zwraca yes lub no w zależności od tego czy przeciwnik ustawił już swoje statki
    $gameid = $conn->real_escape_string($_GET['gameid']);
    $pl = $conn->real_escape_string($_GET['player']);
    if ($pl == 1)
    {
        $res = $conn->query("SELECT * FROM p$gameid WHERE filled = 1;");
        if ($res->num_rows == 11)
        {
            echo "yes";
        }
        else echo "no";
    }
    else
    {
        $res = $conn->query("SELECT * FROM p$gameid WHERE filled2 = 1;");
        if ($res->num_rows == 11)
        {
            echo "yes";
        }
        else echo "no";
    }
}

if (isset($_GET['action']) && $_GET['action'] == "hit" && isset($_GET['player']) && isset($_GET['gameid']) && isset($_GET['id']) )
{
    // Akcja hit:
    // Argumenty: action - musi być ustawiony na 'hit'
    // gameid - numer gry (nr planszy w bazie danych)
    // player - 1 lub 2 zależnie od tego który gracz wysyła pozycje swoich statków
    // id - pole w które strzela
    // Odpowiedź to: missed - pudło, hit - trafienie, win - wygrywające trafienie
    $ah = 1;
    $id = $conn->real_escape_string($_GET['id']);
    $pl = $conn->real_escape_string($_GET['player']);
    $gid = $conn->real_escape_string($_GET['gameid']);
    if ($pl == 1)
    {
        $conn->query("UPDATE p$gid SET hit=1 WHERE id = $id;");
        $conn->query("UPDATE p$gid SET hit=$id WHERE id = 0;");
        $res = $conn->query("SELECT filled2 FROM p$gid WHERE id = $id;");
        $row = $res->fetch_row();
        if ($row[0] == 1)
        {
            $res = $conn->query("SELECT hit FROM p$gid WHERE filled2=1 AND id!=0;");
            for ($i = 0; $i < $res->num_rows; $i++)
            {
                $row = $res->fetch_row();
                if ($row[0] == 0) $ah = 0;
            }
            if ($ah == 1) echo "win";
            else echo "hit";
        }
        else echo "missed";
    }
    else
    {
        $conn->query("UPDATE p$gid SET hit2=1 WHERE id = $id;");
        $conn->query("UPDATE p$gid SET hit2=$id WHERE id = 0;");
        $res = $conn->query("SELECT filled FROM p$gid WHERE id = $id;");
        $row = $res->fetch_row();
        if ($row[0] == 1)
        {
            $res = $conn->query("SELECT hit2 FROM p$gid WHERE filled=1 AND id!=0;");
            for ($i = 0; $i < $res->num_rows; $i++)
            {
                $row = $res->fetch_row();
                if ($row[0] == 0) $ah = 0;
            }
            if ($ah == 1) echo "win";
            else echo "hit";
        }
        else echo "missed";
    }
}

if (isset($_GET['action']) && $_GET['action'] === "lastophit" && isset($_GET['player']) && isset($_GET['gameid']))
{
    $pl = $conn->real_escape_string($_GET['player']);
    $gid = $conn->real_escape_string($_GET['gameid']);
    if ($pl == 1)
    {
        $res = $conn->query("SELECT hit2 FROM p$gid WHERE id=0;");
        $row = $res->fetch_row();
        echo $row[0];
    }
    else
    {
        $res = $conn->query("SELECT hit FROM p$gid WHERE id=0;");
        $row = $res->fetch_row();
        echo $row[0];
    }
}

if (isset($_GET['reset']) && $_GET['reset'] === "1")
{
    session_destroy(); // Akcja 'reset' resetuje grę niszcząc sesję
}

$conn->close(); //Zamykamy połączenie z bazą
?>