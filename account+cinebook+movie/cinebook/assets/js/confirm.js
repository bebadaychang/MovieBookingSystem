// assets/js/confirm.js
// Đọc ghế đã chọn từ sessionStorage (do seats.js lưu trước đó),
// hiển thị thông tin, gọi API tạo booking, xử lý nút Thanh toán

let currentBookingId = null;

document.addEventListener('DOMContentLoaded', () => {
    const seatsData = sessionStorage.getItem('selectedSeats');

    if (!seatsData) {
        document.getElementById('confirm-content').innerHTML =
            '<p>Không có dữ liệu ghế. Vui lòng quay lại chọn ghế.</p>';
        return;
    }

    const selectedSeats = JSON.parse(seatsData);
    renderConfirmInfo(selectedSeats);
    createBooking(selectedSeats);
});

// Hiển thị tạm thông tin ghế trong lúc chờ gọi API
function renderConfirmInfo(seats) {
    const labels = seats.map(s => s.label).join(', ');
    const total = seats.reduce((sum, s) => sum + s.price, 0);

    document.getElementById('confirm-content').innerHTML = `
        <p><strong>Ghế đã chọn:</strong> ${labels}</p>
        <p><strong>Tổng tiền:</strong> <span style="color:#ef4444">${total.toLocaleString('vi-VN')}đ</span></p>
        <p id="booking-status" style="color:#888;font-size:13px;margin-top:8px;">Đang giữ ghế...</p>
    `;
}

// Gọi API tạo booking (status = pending) ngay khi vào trang xác nhận
function createBooking(seats) {
    fetch('../../api/book_ticket.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            showtime_id: SHOWTIME_ID,
            user_id: USER_ID,
            seats: seats.map(s => ({ id: s.id, price: s.price }))
        })
    })
    .then(res => res.json())
    .then(data => {
        const statusEl = document.getElementById('booking-status');
        if (data.success) {
            currentBookingId = data.booking_id;
            statusEl.textContent = 'Đã giữ ghế thành công. Vui lòng thanh toán trong 10 phút.';
            statusEl.style.color = '#22c55e';
            document.getElementById('payment-section').style.display = 'block';
        } else {
            statusEl.textContent = data.message || 'Có lỗi xảy ra.';
            statusEl.style.color = '#ef4444';
            // Ghế đã bị người khác đặt -> quay lại trang chọn ghế
            setTimeout(() => {
                alert(data.message);
                window.location.href = 'seats.php?showtime_id=' + SHOWTIME_ID;
            }, 1500);
        }
    })
    .catch(err => {
        console.error(err);
        document.getElementById('booking-status').textContent = 'Lỗi kết nối server.';
    });
}

// Chọn phương thức thanh toán
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('pay-method')) {
        document.querySelectorAll('.pay-method').forEach(b => b.classList.remove('active'));
        e.target.classList.add('active');
    }
});

// Xác nhận thanh toán (demo: chỉ đổi status, chưa tích hợp cổng thanh toán thật)
function confirmPayment() {
    if (!currentBookingId) {
        alert('Chưa có thông tin đặt vé.');
        return;
    }

    fetch('../../api/update_payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ booking_id: currentBookingId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            sessionStorage.removeItem('selectedSeats');
            window.location.href = 'success.php?booking_id=' + currentBookingId;
        } else {
            alert(data.message || 'Thanh toán thất bại.');
        }
    });
}
