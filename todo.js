console.log("todo.js loaded");

const apiUrl = 'todo-api.php';
const messageDiv = document.getElementById('message');

const showMessage = (message) => {
    // Show error message
    messageDiv.textContent = message;
    messageDiv.style.visibility = 'visible';

    // Hide message after 3 seconds
    setTimeout(() => {
        messageDiv.style.visibility = 'hidden';
    }, 3000);
};

const getDeleteButton = (item) => {
    const deleteButton = document.createElement('button');
    deleteButton.textContent = 'Löschen';

    // Handle delete button click
    deleteButton.addEventListener('click', function() {
        console.log(`Delete ${item.title}`)
        fetch(apiUrl, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: item.id })
        })
        .then(response => response.json())
        .then(() => {
            fetchTodos(); // Reload todo list
        });
    });

    return deleteButton;
}

const getCompletedButton = (item) => {
    const completeButton = document.createElement('button');

    if (item.completed) {
        completeButton.textContent = 'Unerledigt';
    } else {
        completeButton.textContent = 'Erledigt';
    }

    // Handle delete button click
    completeButton.addEventListener('click', function() {
        console.log(`Delete ${item.title}`)
        fetch(apiUrl, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: item.id, completed: !item.completed })
        })
        .then(response => response.json())
        .then(() => {
            fetchTodos(); // Reload todo list
        });
    });

    return completeButton;
}

const getUpdateButton = (item) => {
    const updateButton = document.createElement('button');
    updateButton.textContent = 'Update';

    updateButton.addEventListener('click', function() {
        console.log("update");
        document.getElementById('todo-update-id').value = item.id;
        document.getElementById('todo-update-input').value = item.title;
        document.getElementById('todo-update-form').style.display = 'block';
    });

    return updateButton;
}

document.getElementById('todo-update-form').addEventListener('submit', function (e) {
    // Don't do the default submit action, like send the request.
    e.preventDefault();

    const todoId = document.getElementById('todo-update-id').value;
    const todoInput = document.getElementById('todo-update-input').value;

    fetch(apiUrl, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: todoId, title: todoInput }),
    })
    .then(response => response.json())
    .then((data) => {
        // Handle backend validation errors
        if (data.status === 'error') {
            showMessage(data.message);
        } else {
            fetchTodos();
                document.getElementById('todo-update-id').value = '';
                document.getElementById('todo-update-input').value = '';
                document.getElementById('todo-update-form').style.display = 'none';
        }
    });
});

document.getElementById('todoForm').addEventListener('submit', function (e) {
    // Don't do the default submit action, like send the request.
    e.preventDefault();

    const todoInput = document.getElementById('todoInput').value;

    // Input validation: check if todo is empty or only whitespace
    if (!todoInput || todoInput.trim() === '') {
        showMessage('Bitte geben Sie einen Namen für das TODO an! (Client-Validierung)');
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
        // Handle backend validation errors
        if (data.status === 'error') {
            showMessage(data.message);
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
                if (todo.completed) {
                    li.style.textDecoration = 'line-through';
                }
                li.appendChild(getCompletedButton(todo));
                li.appendChild(getUpdateButton(todo));
                li.appendChild(getDeleteButton(todo));
                todoList.appendChild(li);
            });
        });
}

// initial loading of todo list
window.addEventListener("load", (event) => {
    fetchTodos();
});
