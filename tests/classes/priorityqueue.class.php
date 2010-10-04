<?php
$GLOBALS['client'] = 'Tests';
echo "<p>Starting PriorityQueue test.</p>";

echo "New PriorityQueue, ";
$pq = new PriorityQueue();
echo "done.";
$pq->renderInternalRepresentation();
echo "<br>";

echo "Insert (1, 'one') into pq, ";
$pq->insert(1, 'one');
echo "done.";
$pq->renderInternalRepresentation();
echo "<br>";

echo "Insert (2, 'two') into pq, ";
$pq->insert(2, 'two');
echo "done.";
$pq->renderInternalRepresentation();
echo "<br>";

echo "Insert (0, 'zero') into pq, ";
$pq->insert(0, 'zero');
echo "done.";
$pq->renderInternalRepresentation();
echo "<br>";

echo "Top of pq: ";
var_dump($pq->top());
$pq->renderInternalRepresentation();
echo "<br>";

echo "Pop pq: ";
var_dump($pq->pop());
$pq->renderInternalRepresentation();
echo "<br>";

echo "Pop pq: ";
var_dump($pq->pop());
$pq->renderInternalRepresentation();
echo "<br>";

echo "Pop pq: ";
var_dump($pq->pop());
$pq->renderInternalRepresentation();
echo "<br>";

echo "Pop pq: ";
var_dump($pq->pop());
$pq->renderInternalRepresentation();
echo "<br>";

echo "Stable and FIFO? ";
$pq->insert(0, 1);
$pq->insert(0, 2);
$pq->insert(1, 'one');
$pq->insert(0, 3);
$pq->insert(1, 'two');
$pq->insert(0, 4);
echo $pq->pop();
echo $pq->pop();
echo $pq->pop();
echo $pq->pop();
echo $pq->pop();
echo $pq->pop();

