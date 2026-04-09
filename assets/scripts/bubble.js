// Constants
const BUBBLE_COUNT = 20;
const bubbleLayer = document.getElementById('bubble-layer');

if (!bubbleLayer) {
    return;
}

const viewportWidth = window.innerWidth;
const viewportHeight = window.innerHeight;
const bubbleSize = viewportWidth <= 680 ? 220 : 300;

// Create bubbles
for (let i = 0; i < BUBBLE_COUNT; i++) {
    
    // Create bubble
    const bubble = document.createElement('div');
    bubble.classList.add('bubble');
    if (randomIntFromRange(0, 1) === 0) {
        bubble.classList.add('blue-bubble');
    } else {
        bubble.classList.add('red-bubble');
    }
    bubbleLayer.appendChild(bubble);

    const maxLeft = Math.max(0, viewportWidth - bubbleSize);
    const maxTop = Math.max(0, viewportHeight - bubbleSize);

    bubble.style.left = `${randomIntFromRange(0, maxLeft)}px`;
    bubble.style.top = `${randomIntFromRange(0, maxTop)}px`;

}

// Generate random number
function randomIntFromRange(min, max) {
    return Math.floor(Math.random() * (max - min + 1) + min);
}
