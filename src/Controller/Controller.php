<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Controller extends AbstractController
{
    const TASKS_JSON_PATH = __DIR__ . "/tasks.json";
    /**
     * @Route("/", name="index")
     */
    public function index(): JsonResponse
    {
        return $this->json($this->loadTasks());
    }

    /**
     * @Route("/new", name="new")
     */
    public function new(): Response
    {
        $request = Request::createFromGlobals();
        $tasks = $this->loadTasks();
        $newTask = ["id" => count($tasks), "name" => $request->get("name"), "description" => $request->get("description"), "list" => 0];
        $tasks[] = $newTask;
        $this->saveTasks($tasks);

        return $this->json($tasks);
    }

    /**
     * @Route("/update", name="update")
     */
    public function update(): Response
    {
        $request = Request::createFromGlobals();
        $tasks = $this->loadTasks();


        foreach ($tasks as $nr => $task) {
            if($task["id"] == $request->get("id")) {
                $task["name"] = $request->get("name") !== null ? $request->get("name") : $task["name"];
                $task["description"] = $request->get("description") !== null ? $request->get("description") !== null : $task["description"];
                $task["list"] = intval($request->get("list"));
                $tasks[$nr] = $task;
                break;
            }
        }

        $this->saveTasks($tasks);
        return $this->json($tasks);
    }

    /**
     * @Route("/output", name="output")
     */
    public function taskOutput(Request $request)
    {


        return $this->json($this->loadTasks());
    }


    private function loadTasks() {
        $contentArray = file_get_contents(self::TASKS_JSON_PATH);
        if($contentArray == "") return [];
        return json_decode($contentArray, true);
    }

    private function saveTasks($tasks) {
        $tasksAsJson = json_encode($tasks);
        file_put_contents(self::TASKS_JSON_PATH, $tasksAsJson);
    }

}
