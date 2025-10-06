console.log("todo.js loaded");

const apiUrl = 'todo-api.php';
const messageDiv = document.getElementById('message');

document.getElementById('todoForm').addEventListener('submit', function (e) {

    e.preventDefault();

    const todoInput = document.getElementById('todoInput').value;

    // Input validation: check if todo is empty or only whitespace
    if (!todoInput || todoInput.trim() === '') {
        // Show error message
        message.textContent = 'Bitte geben Sie einen Namen für das TODO an!';
        message.style.visibility = 'visible';

        // Hide message after 3 seconds
        setTimeout(() => {
            message.style.visibility = 'hidden';
        }, 3000);

        // Stop execution if validation fails
        return;
    }

    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ todo: todoInput }),
    })
    .then(response => response.json())
    .then((data) => {

        console.log(data);

        // Handle backend validation errors
        if (data.status === 'error') {
            message.textContent = data.message;
            message.style.visibility = 'visible';

            setTimeout(() => {
                message.style.visibility = 'hidden';
            }, 3000);
        } else {
            fetchTodos();
            document.getElementById('todoInput').value = '';
        }
    });
});

// fetch all todos and present it in a HTML list
function fetchTodos() {
    fetch(apiUrl)
        .then(response => response.json())
        .then(todos => {
            const todoList = document.getElementById('todoList');
            todoList.innerHTML = '';
            todos.forEach(todo => {
                const li = document.createElement('li');
                li.textContent = todo.title;
                todoList.appendChild(li);
            });
        });
}

// initial loading of todo list
window.addEventListener("load", (event) => {
    fetchTodos();
});
