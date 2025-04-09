// write your JavaScript here
const openBtn = document.getElementById('openBtn');
const overlay = document.getElementById('overlay');
const closeBtn = document.getElementById('closeBtn');
const cancelBtn = document.getElementById('cancelBtn');
const submitBtn = document.getElementById('submitBtn');
const ratingBox = document.getElementById('ratingBox');

let selected = null;

// Create buttons for 1 through 10
for (let i = 1; i <= 10; i++) {
  const btn = document.createElement('button');
  btn.textContent = i;

  btn.onclick = () => {
    selected = i;
    [...ratingBox.children].forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
  };

  ratingBox.appendChild(btn);
}

function closeModal() {
  overlay.style.display = 'none';
  selected = null;
  [...ratingBox.children].forEach(b => b.classList.remove('selected'));
}

openBtn.onclick = () => {
  overlay.style.display = 'flex';
};

closeBtn.onclick = closeModal;
cancelBtn.onclick = closeModal;

overlay.onclick = (e) => {
  if (e.target === overlay) closeModal();
};

submitBtn.onclick = () => {
  if (selected) {
    // Send rating to backend
    fetch("submit_rating.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ rating: selected })
    })
    .then(response => {
      if (!response.ok) {
        return response.json().then(err => { throw new Error(err.message); });
      }
      return response.json();
    })
    .then(data => {
      alert(data.message);  // e.g., "Rating submitted successfully."
      closeModal();
    })
    .catch(error => {
      console.error("Submission error:", error);
      alert(error.message || "Error in submitting your rating. Try again later.");
    });
  } else {
    alert('Please choose a rating first.');
  }
};