<?php

function gettag()
{
    return trim(shell_exec('git tag -l --points-at HEAD'));
}

function getcommit()
{
    return trim(shell_exec('git rev-parse HEAD'));
}