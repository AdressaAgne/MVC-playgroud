<?php

function dd(...$args) {
	@header("content-type: application/json");
    die(json_encode($args));
}
