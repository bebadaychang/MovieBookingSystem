// assets/js/review.js
// Xử lý: load thống kê rating, chọn sao để đánh giá, gửi review, render danh sách review

let selectedRating = 0;

document.addEventListener('DOMContentLoaded', () => {
    loadReviews();
    setupStarInput();
});

// 1. Tải dữ liệu thống kê + danh sách đánh giá từ API
function loadReviews() {
    fetch(`../../api/get_reviews.php?movie_id=${MOVIE_ID}`)
        .then(res => res.json())
        .then(data => {
            renderStats(data);
            renderReviewList(data.reviews);
        })
        .catch(err => console.error(err));
}

// 2. Hiển thị điểm trung bình + thanh phần trăm theo từng mức sao
function renderStats(data) {
    document.getElementById('avg-score').textContent = data.average + '/5';
    document.getElementById('total-count').textContent = data.total + ' đánh giá';

    let barsHtml = '';
    for (let star = 5; star >= 1; star--) {
        const percent = data.distribution[star] || 0;
        barsHtml += `
            <div style="display:flex; align-items:center; gap:6px; font-size:12px; margin-bottom:4px;">
                <span style="width:14px;">${star}★</span>
                <div style="flex:1; background:#333; border-radius:4px; height:8px; overflow:hidden;">
                    <div style="width:${percent}%; background:#f59e0b; height:100%;"></div>
                </div>
                <span style="width:30px; text-align:right; color:#888;">${percent}%</span>
            </div>
        `;
    }
    document.getElementById('distribution-bars').innerHTML = barsHtml;
}

// 3. Render danh sách bình luận đánh giá
function renderReviewList(reviews) {
    if (reviews.length === 0) {
        document.getElementById('review-list').innerHTML = '<p style="color:#888;">Chưa có đánh giá nào.</p>';
        return;
    }

    let html = '';
    reviews.forEach(r => {
        const stars = '★'.repeat(r.rating) + '☆'.repeat(5 - r.rating);
        const date = new Date(r.created_at).toLocaleDateString('vi-VN');
        html += `
            <div class="review-item">
                <span class="name">${escapeHtml(r.user_name)}</span>
                <span class="date">${date}</span>
                <div class="review-stars">${stars}</div>
                <p>${escapeHtml(r.comment || '')}</p>
            </div>
        `;
    });
    document.getElementById('review-list').innerHTML = html;
}

// 4. Xử lý chọn sao trong form đánh giá (bấm vào sao thứ mấy thì tô đến đó)
function setupStarInput() {
    const stars = document.querySelectorAll('#star-input span');
    stars.forEach(star => {
        star.addEventListener('click', () => {
            selectedRating = parseInt(star.dataset.star);
            updateStarDisplay();
        });
    });
}

function updateStarDisplay() {
    const stars = document.querySelectorAll('#star-input span');
    stars.forEach(star => {
        const starValue = parseInt(star.dataset.star);
        star.textContent = starValue <= selectedRating ? '★' : '☆';
        star.style.color = starValue <= selectedRating ? '#f59e0b' : '#888';
    });
}

// 5. Gửi đánh giá lên server
function submitReview() {
    const comment = document.getElementById('comment-input').value.trim();
    const msgEl = document.getElementById('review-msg');

    if (selectedRating === 0) {
        msgEl.textContent = 'Vui lòng chọn số sao đánh giá.';
        msgEl.style.color = '#ef4444';
        return;
    }

    fetch('../../api/submit_review.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            movie_id: MOVIE_ID,
            user_id: USER_ID,
            user_name: USER_NAME,
            rating: selectedRating,
            comment: comment
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            msgEl.textContent = 'Gửi đánh giá thành công!';
            msgEl.style.color = '#22c55e';
            // Reset form
            selectedRating = 0;
            updateStarDisplay();
            document.getElementById('comment-input').value = '';
            // Tải lại danh sách + thống kê
            loadReviews();
        } else {
            msgEl.textContent = data.message || 'Có lỗi xảy ra.';
            msgEl.style.color = '#ef4444';
        }
    })
    .catch(err => {
        msgEl.textContent = 'Lỗi kết nối server.';
        msgEl.style.color = '#ef4444';
        console.error(err);
    });
}

// Hàm chống XSS đơn giản khi chèn text từ user vào HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
