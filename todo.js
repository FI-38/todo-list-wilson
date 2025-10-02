console.log("todo.js loaded");

document.getElementById('todoForm').addEventListener('submit', function (e) {

    e.preventDefault();

    const todoInput = document.getElementById('todoInput').value;

    fetch('todo-api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ todo: todoInput }),
    })
    .then(response => response.json())
    .then((data) => {
        console.log(data);
        fetchTodos();
        document.getElementById('todoInput').value = '';
    });
});

// fetch all todos and present it in a HTML list
function fetchTodos() {
    fetch('todo-api.php')
        .then(response => response.json())
        .then(todos => {
            const todoList = document.getElementById('todoList');
            todoList.innerHTML = '';
            todos.forEach(todo => {
                const li = document.createElement('li');
                li.textContent = todo;
                todoList.appendChild(li);
            });
        });
}

// initial loading of todo list
window.addEventListener("load", (event) => {
    fetchTodos();
});