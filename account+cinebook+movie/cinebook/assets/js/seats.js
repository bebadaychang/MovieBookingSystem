// assets/js/seats.js
// Xử lý sơ đồ ghế: gọi API lấy data, render lưới ghế, xử lý click chọn/bỏ chọn

let showtimeInfo = null;
let selectedSeats = []; // mảng các object ghế đang được chọn

// 1. Gọi API lấy dữ liệu ghế khi trang load
fetch(`../../api/get_seats.php?showtime_id=${SHOWTIME_ID}`)
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            document.getElementById('seat-map').innerHTML = `<p>Lỗi: ${data.error}</p>`;
            return;
        }
        showtimeInfo = data.showtime;
        renderSeatMap(data.seats);
    })
    .catch(err => {
        document.getElementById('seat-map').innerHTML = `<p>Không tải được sơ đồ ghế.</p>`;
        console.error(err);
    });

// 2. Render sơ đồ ghế thành lưới theo hàng
function renderSeatMap(seats) {
    // Gom ghế theo từng hàng (A, B, C...)
    const rows = {};
    seats.forEach(seat => {
        if (!rows[seat.row]) rows[seat.row] = [];
        rows[seat.row].push(seat);
    });

    let html = '';
    for (const rowLabel in rows) {
        html += `<div class="seat-row">`;
        html += `<span class="row-label">${rowLabel}</span>`;
        rows[rowLabel].forEach(seat => {
            let cssClass = 'seat';
            if (seat.booked) cssClass += ' booked';
            else if (seat.type === 'vip') cssClass += ' vip';

            html += `<button
                class="${cssClass}"
                data-id="${seat.id}"
                data-label="${seat.label}"
                data-type="${seat.type}"
                ${seat.booked ? 'disabled' : ''}
                onclick="toggleSeat(this)"
            >${seat.col}</button>`;
        });
        html += `</div>`;
    }

    document.getElementById('seat-map').innerHTML = html;
}

// 3. Xử lý khi người dùng click vào 1 ghế
function toggleSeat(btn) {
    const seatId = btn.dataset.id;
    const label = btn.dataset.label;
    const type = btn.dataset.type;
    const price = type === 'vip' ? showtimeInfo.price_vip : showtimeInfo.price_normal;

    const index = selectedSeats.findIndex(s => s.id === seatId);

    if (index === -1) {
        // Chưa chọn -> thêm vào danh sách, tô màu đỏ (đang chọn)
        selectedSeats.push({ id: seatId, label, price });
        btn.classList.add('selected');
    } else {
        // Đã chọn -> bỏ chọn
        selectedSeats.splice(index, 1);
        btn.classList.remove('selected');
    }

    updateSummary();
}

// 4. Cập nhật phần tóm tắt: danh sách ghế đã chọn + tổng tiền
function updateSummary() {
    const listEl = document.querySelector('#selected-list span');
    const totalEl = document.querySelector('#total-price strong');
    const btnContinue = document.getElementById('btn-continue');

    if (selectedSeats.length === 0) {
        listEl.textContent = 'chưa chọn ghế nào';
        totalEl.textContent = '0đ';
        btnContinue.disabled = true;
        return;
    }

    const labels = selectedSeats.map(s => s.label).join(', ');
    const total = selectedSeats.reduce((sum, s) => sum + s.price, 0);

    listEl.textContent = labels;
    totalEl.textContent = total.toLocaleString('vi-VN') + 'đ';
    btnContinue.disabled = false;
}

// 5. Chuyển sang trang xác nhận, mang theo dữ liệu ghế đã chọn
function goToConfirm() {
    // Lưu tạm vào sessionStorage để trang confirm.php đọc được
    sessionStorage.setItem('selectedSeats', JSON.stringify(selectedSeats));
    sessionStorage.setItem('showtimeId', SHOWTIME_ID);
    window.location.href = 'confirm.php?showtime_id=' + SHOWTIME_ID;
}
