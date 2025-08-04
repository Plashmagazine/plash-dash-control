<?php
/**
 * Bloquear acesso direto à pasta includes
 */
header('HTTP/1.0 403 Forbidden');
exit('Acesso negado');
?>