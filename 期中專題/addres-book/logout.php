<?php

session_start();

unset($_SESSION["admin"]);

header('Location: members_index_.php');
