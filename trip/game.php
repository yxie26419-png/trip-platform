<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Game - Travel Journey</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .game-container {
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }
        .game-board {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 20px auto;
            max-width: 400px;
        }
        .card {
            width: 80px;
            height: 80px;
            background-color: #333;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .card.flipped {
            background-color: #fff;
            color: #333;
        }
        .card.matched {
            background-color: #4CAF50;
            cursor: default;
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-container">
            <h1>Travel Journey</h1>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <div class="game-container">
            <h2>Travel Memory Game</h2>
            <p>Match the travel icons! Click on cards to flip them.</p>
            <div id="game-board" class="game-board"></div>
            <div id="game-info">
                <p>Moves: <span id="moves">0</span></p>
                <p>Matches: <span id="matches">0</span>/8</p>
                <button id="reset-btn" class="btn">Reset Game</button>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Travel Journey Sharing. All rights reserved.</p>
    </footer>

    <script>
        const icons = ['✈️', '🏖️', '🏔️', '🚗', '🗺️', '📸', '🎒', '🏕️'];
        let cards = [];
        let flippedCards = [];
        let matchedPairs = 0;
        let moves = 0;

        function createBoard() {
            const board = document.getElementById('game-board');
            board.innerHTML = '';
            cards = [];
            flippedCards = [];
            matchedPairs = 0;
            moves = 0;
            updateInfo();

            const gameIcons = [...icons, ...icons].sort(() => Math.random() - 0.5);

            gameIcons.forEach((icon, index) => {
                const card = document.createElement('div');
                card.className = 'card';
                card.dataset.icon = icon;
                card.dataset.index = index;
                card.addEventListener('click', flipCard);
                board.appendChild(card);
                cards.push(card);
            });
        }

        function flipCard() {
            if (flippedCards.length < 2 && !this.classList.contains('flipped') && !this.classList.contains('matched')) {
                this.classList.add('flipped');
                this.textContent = this.dataset.icon;
                flippedCards.push(this);

                if (flippedCards.length === 2) {
                    moves++;
                    updateInfo();
                    setTimeout(checkMatch, 1000);
                }
            }
        }

        function checkMatch() {
            const [card1, card2] = flippedCards;
            if (card1.dataset.icon === card2.dataset.icon) {
                card1.classList.add('matched');
                card2.classList.add('matched');
                matchedPairs++;
                updateInfo();
                if (matchedPairs === 8) {
                    setTimeout(() => alert('Congratulations! You won in ' + moves + ' moves!'), 500);
                }
            } else {
                card1.classList.remove('flipped');
                card2.classList.remove('flipped');
                card1.textContent = '';
                card2.textContent = '';
            }
            flippedCards = [];
        }

        function updateInfo() {
            document.getElementById('moves').textContent = moves;
            document.getElementById('matches').textContent = matchedPairs;
        }

        document.getElementById('reset-btn').addEventListener('click', createBoard);

        // Initialize game
        createBoard();
    </script>

    <script src="script.js"></script>
</body>
</html>