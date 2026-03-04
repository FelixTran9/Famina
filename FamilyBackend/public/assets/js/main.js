// --- CẤU HÌNH API ---
const API_URL = '/api'; // Địa chỉ Backend của bạn

// Global state for categories and products
let allProducts = [];
let allCategories = [];
let selectedCategory = null;

// --- DYNAMIC CONTENT LOADER ---
// Tự động tải header, footer, và modal
document.addEventListener('DOMContentLoaded', () => {
    // Determine a safe rootPath to load templates from. The previous heuristic (./ vs ../)
    // fails when pages are opened via file:// or when the path contains unexpected segments.
    // Strategy:
    //  - If the last path segment is empty or 'index.html' treat this as the site root (use './').
    //  - Otherwise compute how many '../' segments are needed to reach the repository root.
    const pathParts = window.location.pathname.split('/');
    const lastSegment = pathParts[pathParts.length - 1] || '';
    const isIndex = lastSegment === '' || lastSegment === 'index.html';

    let rootPath;
    if (isIndex) {
        rootPath = './';
    } else {
        // Example: '/pages/account.html' -> ['', 'pages', 'account.html'] -> need one '../'
        const upLevels = Math.max(0, pathParts.length - 2);
        rootPath = upLevels > 0 ? '../'.repeat(upLevels) : '../';
    }
    console.log('[template-loader] pathname=', window.location.pathname, 'isIndex=', isIndex, 'rootPath=', rootPath);



    async function loadProducts() {
        // Chỉ chạy hàm này nếu chúng ta ở trang chủ
        const productListContainer = document.getElementById('product-list');
        if (!productListContainer) {
            console.log('Không ở trang sản phẩm, bỏ qua tải sản phẩm.');
            return;
        }

        try {
            // Load categories
            await loadCategories();

            // Load products
            const response = await fetch(`${API_URL}/products`);
            if (!response.ok) {
                throw new Error('Lỗi khi tải sản phẩm');
            }

            allProducts = await response.json();
            console.log('✅ Loaded products:', allProducts.length);

            // Render all products initially
            renderProducts(allProducts);

        } catch (error) {
            console.error(error);
            productListContainer.innerHTML = '<p class="text-red-500">Không thể tải sản phẩm. Vui lòng thử lại.</p>';
        }
    }

    async function loadCategories() {
        try {
            const response = await fetch(`${API_URL}/danh-muc`);
            if (!response.ok) throw new Error('Failed to load categories');

            allCategories = await response.json();
            console.log('✅ Loaded categories:', allCategories.length);

            // Render category filter
            renderCategoryFilter();
            // Render sidebar categories if present
            if (typeof renderSidebarCategories === 'function') renderSidebarCategories();
        } catch (error) {
            console.error('❌ Error loading categories:', error);
        }
    }

    function renderCategoryFilter() {
        const dropdownMenu = document.getElementById('dropdownMenu');
        if (!dropdownMenu) return;

        // Clear existing items except the first one
        const existingItems = dropdownMenu.querySelectorAll('a');
        existingItems.forEach(item => {
            if (!item.textContent.includes('Danh mục sản phẩm')) {
                item.remove();
            }
        });

        // Add "Tất cả" button
        const allBtn = document.createElement('a');
        allBtn.href = '#';
        allBtn.className = 'block px-4 py-2 text-gray-700 hover:bg-cyan-100';
        allBtn.textContent = '📦 Tất cả sản phẩm';
        allBtn.onclick = (e) => {
            e.preventDefault();
            selectedCategory = null;
            renderProducts(allProducts);
            toggleDropdown();
        };
        dropdownMenu.appendChild(allBtn);

        // Add category items
        allCategories.forEach(cat => {
            const link = document.createElement('a');
            link.href = '#';
            link.className = 'block px-4 py-2 text-gray-700 hover:bg-cyan-100';
            link.textContent = cat.name || cat.TenDanhMuc || 'Unknown';
            link.onclick = (e) => {
                e.preventDefault();
                selectedCategory = cat.id || cat.MaDanhMuc;
                const filtered = allProducts.filter(p => p.category_id === selectedCategory);
                renderProducts(filtered);
                toggleDropdown();
            };
            dropdownMenu.appendChild(link);
        });
    }

    // Render categories in page sidebar (for pages like special-offers.html)
    function renderSidebarCategories() {
        const container = document.getElementById('specialCategories');
        if (!container) return;
        container.innerHTML = '';

        const allLink = document.createElement('a');
        allLink.href = '#';
        allLink.className = 'block px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700';
        allLink.textContent = '📦 Tất cả sản phẩm';
        allLink.onclick = (e) => {
            e.preventDefault();
            selectedCategory = null;
            renderProducts(allProducts);
        };
        container.appendChild(allLink);

        allCategories.forEach(cat => {
            const a = document.createElement('a');
            a.href = '#';
            a.className = 'block px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700';
            a.textContent = cat.name || cat.TenDanhMuc || 'Unknown';
            a.onclick = (e) => {
                e.preventDefault();
                selectedCategory = cat.id || cat.MaDanhMuc;
                const filtered = allProducts.filter(p => p.category_id === selectedCategory);
                renderProducts(filtered);
                // scroll to products
                const list = document.getElementById('product-list');
                if (list) list.scrollIntoView({ behavior: 'smooth' });
            };
            container.appendChild(a);
        });
    }



    // Định nghĩa các tệp mẫu và nơi đặt chúng
    const templates = [
        { id: '#header-placeholder', path: `${rootPath}templates/header.html` },
        { id: '#modal-placeholder', path: `${rootPath}templates/_auth_modal.html` },
        { id: '#footer-placeholder', path: `${rootPath}templates/footer.html` }
    ];

    // Hàm fetch và chèn HTML (với nhiều đường dẫn thử nghiệm và logging)
    const loadTemplate = async (template) => {
        const placeholder = document.querySelector(template.id);
        if (!placeholder) return; // Không tìm thấy, bỏ qua

        // Build candidate paths to try (original + common fallbacks)
        const candidates = [];
        candidates.push(template.path);

        // If template.path contains './templates/' or '../templates/' add the opposite variant
        if (template.path.includes('./templates/')) {
            candidates.push(template.path.replace('./templates/', '../templates/'));
        } else if (template.path.includes('../templates/')) {
            candidates.push(template.path.replace('../templates/', './templates/'));
        }

        // Try absolute path from origin and site-root path
        const nameOnly = template.path.split('/').pop(); // header.html
        candidates.push(`/templates/${nameOnly}`);

        // Also try with the page origin + templates (useful when served)
        try {
            const originPrefixed = `${window.location.origin}/templates/${nameOnly}`;
            if (!candidates.includes(originPrefixed)) candidates.push(originPrefixed);
        } catch (e) {
            // window.location.origin might be 'null' on file:// in some browsers; ignore
        }

        // Deduplicate candidates while keeping order
        const tried = [];
        for (const c of candidates) if (!tried.includes(c)) tried.push(c);

        let loaded = false;
        for (const p of tried) {
            console.log(`[template-loader] trying fetch: ${p}`);
            try {
                const resp = await fetch(p);
                if (!resp.ok) {
                    console.warn(`[template-loader] ${p} returned ${resp.status}`);
                    continue; // try next
                }

                const html = await resp.text();

                // Xử lý đường dẫn cho các trang con
                let processedHtml = html;
                if (!isIndex) {
                    processedHtml = html
                        .replace(/href="pages\/(.*?)"/g, 'href="$1"') // pages/account.html -> account.html
                        .replace(/href="index\.html"/g, 'href="..\/index.html"') // index.html -> ../index.html
                        .replace(/src="assets\/(.*?)"/g, 'src="..\/assets\/$1"') // assets/img.png -> ../assets/img.png
                        .replace(/action="index\.html"/g, 'action="..\/index.html"'); // Dành cho logo link
                }

                placeholder.innerHTML = processedHtml;
                console.log(`[template-loader] loaded ${p} into ${template.id}`);
                loaded = true;
                break;
            } catch (err) {
                console.warn(`[template-loader] fetch error for ${p}:`, err);
                // try next
            }
        }

        if (!loaded) {
            console.error(`[template-loader] tất cả các đường dẫn thử nghiệm đều thất bại for ${template.id}. Paths tried: ${tried.join(', ')}`);
            placeholder.innerHTML = `<p class="text-center text-red-500">Lỗi khi tải ${template.id} (paths tried: ${tried.join(', ')})</p>`;
        }
    };

    // Tải tất cả các mẫu CÙNG MỘT LÚC
    Promise.all(templates.map(loadTemplate))
        .then(() => {
            // --- KHỞI TẠO SAU KHI TẢI XONG ---
            console.log('Tất cả mẫu đã tải xong. Khởi tạo script chính...');

            loadProducts(); // <-- GỌI HÀM TẢI SẢN PHẨM Ở ĐÂY

            // CÁC HÀM NÀY NGUYÊN GỐC TỪ CUỐI TỆP CŨ
            if (currentUser) {
                updateUIForLoggedInUser();
            }
            // Khởi tạo trạng thái giỏ hàng trên giao diện ngay sau khi templates + nội dung tải xong
            updateCartBadge();
            updateCartDisplay();

        })
        .catch(error => {
            console.error('Không thể tải các mẫu quan trọng:', error);
        });



});

// -------------------------------------------------------------------
// --- ĐÂY LÀ TẤT CẢ CÁC HÀM CỐT LÕI CỦA BẠN (GIỮ NGUYÊN) ---
// -------------------------------------------------------------------

// --- STATE MANAGEMENT (Sử dụng localStorage để giữ liệu) ---

// Tải trạng thái từ localStorage hoặc khởi tạo
// let users = JSON.parse(localStorage.getItem('familyMartUsers')) || {};



let currentUser = JSON.parse(localStorage.getItem('familyMartCurrentUser')) || null;
// Load and normalize cart from localStorage. Older versions saved different keys;
// ensure every item always has `product_code` (string) and `id` for compatibility.
let cart = JSON.parse(localStorage.getItem('familyMartCart')) || [];
cart = cart.map(item => {
    // If product_code missing but id exists, copy id into product_code
    if (!item.product_code && item.id) {
        item.product_code = item.id;
    }
    // If id missing but product_code exists, keep id in sync
    if (!item.id && item.product_code) {
        item.id = item.product_code;
    }
    return item;
});
// Persist normalization back to localStorage so older entries are fixed for future loads
saveCart();

// Hàm trợ giúp để lưu trạng thái vào localStorage
function saveUsers() {
    localStorage.setItem('familyMartUsers', JSON.stringify(users));
}
function saveCurrentUser() {
    localStorage.setItem('familyMartCurrentUser', JSON.stringify(currentUser));
}
function saveCart() {
    localStorage.setItem('familyMartCart', JSON.stringify(cart));
}

// --- CORE FUNCTIONS (Các hàm gốc đã được sửa đổi) ---

// Toggle Dropdown Menu
function toggleDropdown(event) {
    if (event) event.preventDefault();
    const dropdown = document.getElementById('dropdownMenu');
    dropdown.classList.toggle('active');
}

// Close dropdown when clicking outside
document.addEventListener('click', function (event) {
    const dropdown = document.getElementById('dropdownMenu');
    if (!dropdown) return; // Không làm gì nếu không có dropdown trên trang

    const button = event.target.closest('button');

    if (!dropdown.contains(event.target) && (!button || !button.textContent.includes('Danh mục sản phẩm'))) {
        dropdown.classList.remove('active');
    }
});

// Show Login Modal
function showLoginModal() {
    document.getElementById('authModal').classList.remove('hidden');
    document.getElementById('loginForm').classList.remove('hidden');
    document.getElementById('registerForm').classList.add('hidden');
}

// Close Auth Modal
function closeAuthModal() {
    document.getElementById('authModal').classList.add('hidden');
}

// Switch to Register Form
function switchToRegister() {
    document.getElementById('loginForm').classList.add('hidden');
    document.getElementById('registerForm').classList.remove('hidden');
}

// Switch to Login Form
function switchToLogin() {
    document.getElementById('registerForm').classList.add('hidden');
    document.getElementById('loginForm').classList.remove('hidden');
}


// --- Register Function (GỌI API) ---
async function register() {
    const name = document.getElementById('registerName').value;
    const email = document.getElementById('registerEmail').value;
    const phone = document.getElementById('registerPhone').value;
    const password = document.getElementById('registerPassword').value;

    if (!name || !email || !password) {
        alert('Vui lòng điền đầy đủ thông tin (Tên, Email, Mật khẩu).');
        return;
    }

    // Hiển thị trạng thái đang xử lý (Optional)
    const btn = document.querySelector('#registerForm button[type="submit"]');
    const originalText = btn.textContent;
    btn.textContent = "Đang xử lý...";
    btn.disabled = true;

    try {
        const response = await fetch(`${API_URL}/register`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                email: email,
                password: password,
                phone: phone // Gửi thêm SĐT nếu Backend hỗ trợ
            })
        });

        const data = await response.json();

        if (!response.ok) {
            // Nếu Backend trả về lỗi (ví dụ: Email trùng)
            throw new Error(data.message || 'Đăng ký thất bại');
        }

        // Nếu backend trả token, lưu user và token vào localStorage
        if (data.token) {
            currentUser = data.user;
            currentUser.token = data.token;
            saveCurrentUser();
            updateUIForLoggedInUser();
            closeAuthModal();
        }

        alert('Đăng ký thành công! Vui lòng đăng nhập.');
        switchToLogin(); // Chuyển sang form đăng nhập

    } catch (error) {
        console.error('Register Error:', error);
        alert('Lỗi: ' + error.message);
    } finally {
        // Khôi phục nút bấm
        btn.textContent = originalText;
        btn.disabled = false;
    }
}

// --- Login Function (GỌI API & PHÂN QUYỀN) ---
async function login() {
    const loginId = document.getElementById('loginIdentifier').value;
    const password = document.getElementById('loginPassword').value;

    console.log('Login attempt:', loginId, password); // Debug

    if (!loginId || !password) {
        alert('Vui lòng nhập Tài khoản và Mật khẩu.');
        return;
    }

    const btn = document.querySelector('#loginForm button[type="submit"]');
    const originalText = btn.textContent;
    btn.textContent = "Đang đăng nhập...";
    btn.disabled = true;

    try {
        const response = await fetch(`${API_URL}/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                login_id: loginId,
                password: password
            })
        });

        const data = await response.json();

        console.log('Login response:', data); // Debug

        if (!response.ok) {
            throw new Error(data.message || 'Tài khoản hoặc mật khẩu không đúng.');
        }

        // --- LOGIC MỚI: XỬ LÝ PHÂN QUYỀN ---

        // 1. Nếu Backend bảo đây là Admin/Staff -> Chuyển trang ngay
        if (data.redirect_url) {
            alert('Xin chào Quản lý. Đang chuyển đến trang quản trị...');
            // Chuyển hướng sang trang Admin (Backend Laravel)
            window.location.href = data.redirect_url;
            return;
        }

        // 2. Nếu là Khách hàng -> Lưu token vào LocalStorage và ở lại mua sắm
        currentUser = data.user;
        if (data.token) currentUser.token = data.token;
        saveCurrentUser();
        updateUIForLoggedInUser();
        closeAuthModal();

    } catch (error) {
        console.error('Login Error:', error);
        alert('Đăng nhập thất bại: ' + error.message);
    } finally {
        btn.textContent = originalText;
        btn.disabled = false;
    }
}

// Update UI for Logged In User
function updateUIForLoggedInUser() {
    if (!currentUser) return; // Không có user, không làm gì cả

    // Tìm các phần tử này (có thể không tồn tại trên mọi trang)
    const userSection = document.getElementById('userSection');
    const userLoggedIn = document.getElementById('userLoggedIn');
    const userInitial = document.getElementById('userInitial');
    const userName = document.getElementById('userName');
    const userEmail = document.getElementById('userEmail');

    if (userSection) userSection.classList.add('hidden');
    if (userLoggedIn) userLoggedIn.classList.remove('hidden');

    const initial = currentUser.name ? currentUser.name.charAt(0).toUpperCase() : 'U';
    if (userInitial) userInitial.textContent = initial;
    if (userName) userName.textContent = currentUser.name;
    if (userEmail) userEmail.textContent = currentUser.email;

    // Cập nhật cho trang tài khoản (nếu có)
    const accountInitial = document.getElementById('accountInitial');
    const accountName = document.getElementById('accountName');
    const accountFullName = document.getElementById('accountFullName');
    const accountEmail = document.getElementById('accountEmail');
    const accountPhone = document.getElementById('accountPhone');
    const accountAddress = document.getElementById('accountAddress');

    if (accountInitial) accountInitial.textContent = initial;
    if (accountName) accountName.textContent = currentUser.name;
    if (accountFullName) accountFullName.value = currentUser.name;
    if (accountEmail) accountEmail.value = currentUser.email;
    if (accountPhone) accountPhone.value = currentUser.phone || '';
    if (accountAddress) accountAddress.value = currentUser.address || '';
}

// Toggle User Menu
function toggleUserMenu() {
    const menu = document.getElementById('userMenu');
    if (menu) menu.classList.toggle('hidden');
}

// Close user menu when clicking outside
document.addEventListener('click', function (event) {
    const userMenu = document.getElementById('userMenu');
    if (!userMenu) return; // Không làm gì nếu không có menu

    const userButton = event.target.closest('#userLoggedIn button');

    if (!userMenu.contains(event.target) && !userButton) {
        userMenu.classList.add('hidden');
    }
});

// Logout Function
function logout() {
    currentUser = null;
    saveCurrentUser(); // Xóa khỏi localStorage

    // Tìm các phần tử này
    const userSection = document.getElementById('userSection');
    const userLoggedIn = document.getElementById('userLoggedIn');
    const userMenu = document.getElementById('userMenu');

    if (userSection) userSection.classList.remove('hidden');
    if (userLoggedIn) userLoggedIn.classList.add('hidden');
    if (userMenu) userMenu.classList.add('hidden');

    alert('Đăng xuất thành công!');

    // Chuyển hướng về trang chủ
    // Kiểm tra xem chúng ta đang ở trang chủ hay trang con
    const isIndex = window.location.pathname.endsWith('/') || window.location.pathname === '/' || window.location.pathname.endsWith('/' + window.location.pathname.split('/')[1] + '/');
    if (!isIndex) {
        window.location.href = '/'; // Từ trang con về trang chủ
    } else {
        window.location.reload(); // Tải lại trang chủ
    }
}

// Update Account
async function updateAccount(evt) {
    if (evt) evt.preventDefault();

    if (!currentUser) {
        alert('Vui lòng đăng nhập trước.');
        return;
    }

    const accountFullName = document.getElementById('accountFullName');
    const accountPhone = document.getElementById('accountPhone');
    const accountEmail = document.getElementById('accountEmail');
    const accountAddress = document.getElementById('accountAddress');

    if (!accountFullName || !accountEmail) {
        alert('Không tìm thấy form fields.');
        return;
    }

    const newName = accountFullName.value.trim();
    const newPhone = accountPhone ? accountPhone.value.trim() : '';
    const newAddress = accountAddress ? accountAddress.value.trim() : '';
    const userEmail = accountEmail.value.trim() || currentUser.email;

    // Kiểm tra hợp lệ
    if (!newName || !userEmail) {
        alert('Vui lòng điền đầy đủ thông tin (Tên, Email).');
        return;
    }

    // Hiển thị trạng thái đang xử lý
    const btn = document.querySelector('form button[type="submit"]');
    const originalText = btn ? btn.textContent : 'Cập nhật thông tin';
    if (btn) {
        btn.textContent = 'Đang cập nhật...';
        btn.disabled = true;
    }

    try {
        console.log('📤 Gửi request PUT /api/profile:', {
            email: userEmail,
            name: newName,
            phone: newPhone,
            address: newAddress
        });

        // Gửi request PUT /api/profile để cập nhật backend
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
        if (currentUser && currentUser.token) {
            headers['Authorization'] = `Bearer ${currentUser.token}`;
        }
        const response = await fetch(`${API_URL}/profile`, {
            method: 'PUT',
            headers: headers,
            body: JSON.stringify({
                email: userEmail,
                name: newName,
                phone: newPhone || null,
                address: newAddress || null
            })
        });

        console.log('📨 Response status:', response.status);
        const result = await response.json();
        console.log('📨 Response body:', result);

        if (!response.ok) {
            throw new Error(result.message || `Lỗi ${response.status}: ${result.error || 'Không cập nhật được'}`);
        }

        // Cập nhật localStorage với thông tin mới
        currentUser.name = newName;
        currentUser.phone = newPhone;
        currentUser.address = newAddress;
        currentUser.email = userEmail;
        saveCurrentUser();

        // Cập nhật UI
        updateUIForLoggedInUser();

        alert('✅ Cập nhật thông tin thành công!');

    } catch (error) {
        console.error('❌ Update Account Error:', error);
        alert('❌ Lỗi: ' + error.message);
    } finally {
        if (btn) {
            btn.textContent = originalText;
            btn.disabled = false;
        }
    }
}

// Add to Cart
// --- CẬP NHẬT HÀM ADD TO CART ---
function addToCart(productId, productName, price, image) {
    if (!currentUser) {
        alert('Vui lòng đăng nhập để mua hàng!');
        showLoginModal();
        return;
    }

    // Fix lỗi: Đảm bảo productId là chuỗi (vì Backend trả về String MaSP)
    const code = String(productId);

    // Tìm trong giỏ hàng xem có chưa
    const existingItem = cart.find(item => String(item.product_code) === code);

    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({
            id: code,           // Legacy
            product_code: code, // Quan trọng: Dùng để gửi về Backend
            name: productName,
            price: Number(price),
            image: image,
            quantity: 1
        });
    }

    saveCart();
    updateCartBadge();

    // Hiệu ứng UX nhỏ
    const btn = event.target; // Nút vừa bấm
    const oldText = btn.innerText;
    btn.innerText = "✅ Đã thêm";
    setTimeout(() => btn.innerText = oldText, 1000);
}

/**
 * Lấy một mảng sản phẩm và "vẽ" chúng ra HTML
 */
function renderProducts(products) {
    const container = document.getElementById('product-list');
    if (!container) return; // Không làm gì nếu không tìm thấy khung

    if (products.length === 0) {
        container.innerHTML = '<p class="text-center text-gray-500 py-8">Không tìm thấy sản phẩm phù hợp</p>';
        return;
    }

    container.innerHTML = ''; // Xóa sạch mọi thứ bên trong

    products.forEach(product => {
        // Chuyển đổi giá từ CSDL (ví dụ: "28000.00") thành số và định dạng
        const price = parseFloat(product.price).toLocaleString('vi-VN');

        // Xử lý tên sản phẩm để an toàn khi đặt vào 'onclick'
        const safeName = product.name.replace(/'/g, "\\'");

        // Sử dụng placeholder nếu không có ảnh
        const imageUrl = product.image
            ? `/storage/${product.image}`
            : 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="300"%3E%3Crect fill="%23f0f0f0" width="400" height="300"/%3E%3Ctext x="50%" y="50%" font-family="Arial" font-size="18" fill="%23999" text-anchor="middle" dominant-baseline="middle"%3E📦 No Image%3C/text%3E%3C/svg%3E';

        // Đây là code HTML cho 1 thẻ sản phẩm
        const productCard = `
        <div class="product-card bg-white rounded-2xl overflow-hidden shadow-md border border-gray-100 hover:shadow-lg transition">
            <div class="relative">
                <img src="${imageUrl}" alt="${product.name}" class="w-full h-48 object-cover bg-gray-200 hover:scale-105 transition">
                <div class="absolute top-3 left-3">
                    <span class="bg-cyan-600 text-white text-xs px-2 py-1 rounded-full font-semibold">FamilyMart</span>
                </div>
            </div>
            <div class="p-4">
                <h3 class="font-bold text-gray-800 mb-2 line-clamp-2">${product.name}</h3>
                <div class="text-cyan-600 font-bold text-xl mb-3">${price} đ</div>

                <button onclick="addToCart('${product.product_code}', '${safeName}', ${product.price}, '${imageUrl}')" 
                        class="w-full border-2 border-cyan-600 text-cyan-600 py-2 rounded-lg font-semibold hover:bg-cyan-600 hover:text-white transition">
                    🛒 Thêm vào giỏ
                </button>
            </div>
        </div>
        `;

        // Thêm thẻ sản phẩm mới vào khung
        container.innerHTML += productCard;
    });
}

// Search Products Function
// Search Products Function with server-side support for longer queries
let _searchTimeout = null;
function searchProducts(query) {
    query = (query || '').trim();
    if (!query) {
        renderProducts(allProducts);
        return;
    }

    // For short queries, do client-side filtering to keep UI snappy
    if (query.length < 3) {
        const filtered = allProducts.filter(p => p.name.toLowerCase().includes(query.toLowerCase()));
        renderProducts(filtered);
        return;
    }

    // For longer queries, call the API (debounced)
    if (_searchTimeout) clearTimeout(_searchTimeout);
    _searchTimeout = setTimeout(async () => {
        try {
            const res = await fetch(`${API_URL}/products?q=${encodeURIComponent(query)}`);
            if (!res.ok) {
                // fallback to client-side filter on error
                const fallback = allProducts.filter(p => p.name.toLowerCase().includes(query.toLowerCase()));
                renderProducts(fallback);
                return;
            }
            const data = await res.json();
            // API returns an array of products
            renderProducts(data);
        } catch (err) {
            console.error('Search error:', err);
            const fallback = allProducts.filter(p => p.name.toLowerCase().includes(query.toLowerCase()));
            renderProducts(fallback);
        }
    }, 300);
}

// Add search event listener when page loads
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            searchProducts(e.target.value);
        });
    }
});






// Update Cart Badge
function updateCartBadge() {
    const badge = document.getElementById('cartBadge');
    if (!badge) return;
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    badge.textContent = totalItems;
}

// Update Cart Display (chỉ chạy trên trang giỏ hàng)
function updateCartDisplay() {
    const cartItems = document.getElementById('cartItems');
    if (!cartItems) return; // Chỉ chạy nếu đang ở trang giỏ hàng

    if (cart.length === 0) {
        cartItems.innerHTML = `
            <div class="text-center py-12">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-gray-600 text-lg">Giỏ hàng trống</p>
                <a href="../index.html" class="mt-4 px-6 py-3 bg-cyan-600 text-white rounded-lg font-semibold hover:bg-cyan-700 transition inline-block">
                    Mua sắm ngay
                </a>
            </div>
        `;
        document.getElementById('subtotal').textContent = '0 đ';
        document.getElementById('total').textContent = '20,000 đ';
        return;
    }

    let html = '<h2 class="text-xl font-bold text-gray-800 mb-4">Sản phẩm (' + cart.length + ')</h2>';
    let subtotal = 0;

    cart.forEach((item, index) => {
        subtotal += item.price * item.quantity;
        html += `
            <div class="flex items-center space-x-4 pb-4 mb-4 border-b">
                <img src="${item.image}" alt="${item.name}" class="w-20 h-20 object-cover rounded-lg">
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-800">${item.name}</h4>
                    <p class="text-cyan-600 font-bold">${item.price.toLocaleString()} đ</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="decreaseQuantity(${index})" class="w-8 h-8 border rounded-lg hover:bg-gray-100">-</button>
                    <span class="font-semibold">${item.quantity}</span>
                    <button onclick="increaseQuantity(${index})" class="w-8 h-8 border rounded-lg hover:bg-gray-100">+</button>
                </div>
                <button onclick="removeFromCart(${index})" class="text-red-500 hover:text-red-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        `;
    });

    cartItems.innerHTML = html;

    const total = subtotal + 20000; // Giả sử phí ship 20,000đ
    document.getElementById('subtotal').textContent = subtotal.toLocaleString() + ' đ';
    document.getElementById('total').textContent = total.toLocaleString() + ' đ';
}

// Increase Quantity
function increaseQuantity(index) {
    cart[index].quantity++;
    saveCart(); // Lưu thay đổi
    updateCartDisplay();
    updateCartBadge();
}

// Decrease Quantity
function decreaseQuantity(index) {
    if (cart[index].quantity > 1) {
        cart[index].quantity--;
        saveCart(); // Lưu thay đổi
        updateCartDisplay();
        updateCartBadge();
    }
}

// Remove from Cart
function removeFromCart(index) {
    cart.splice(index, 1);
    saveCart(); // Lưu thay đổi
    updateCartDisplay();
    updateCartBadge();
}



// Thay thế function checkout cũ bằng đoạn này:

async function checkout() {
    if (!currentUser) {
        alert('Vui lòng đăng nhập để thanh toán!');
        showLoginModal();
        return;
    }

    if (cart.length === 0) {
        alert('Giỏ hàng trống!');
        return;
    }

    // --- LOGIC MỚI: KIỂM TRA ĐỊA CHỈ ---
    let shippingAddress = currentUser.address;

    // Nếu chưa có địa chỉ hoặc địa chỉ quá ngắn, yêu cầu nhập
    if (!shippingAddress || shippingAddress.length < 5) {
        shippingAddress = prompt("Vui lòng nhập địa chỉ giao hàng chính xác:", "");

        if (!shippingAddress || shippingAddress.trim().length < 5) {
            alert("Bạn cần cung cấp địa chỉ giao hàng để tiếp tục!");
            return; // Dừng lại, không gọi API
        }

        // Cập nhật tạm vào object currentUser và lưu lại localStorage
        currentUser.address = shippingAddress;
        saveCurrentUser();

        // Cập nhật địa chỉ lên DB để lần sau không cần nhập lại
        try {
            const updateHeaders = {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };
            if (currentUser.token) {
                updateHeaders['Authorization'] = `Bearer ${currentUser.token}`;
            }
            await fetch(`${API_URL}/profile`, {
                method: 'PUT',
                headers: updateHeaders,
                body: JSON.stringify({
                    email: currentUser.email,
                    name: currentUser.name,
                    phone: currentUser.phone,
                    address: shippingAddress
                })
            });
            console.log('Đã cập nhật địa chỉ lên DB');
        } catch (updateError) {
            console.warn('Không thể cập nhật địa chỉ lên DB:', updateError);
            // Không alert vì không muốn làm gián đoạn checkout
        }

        // Cập nhật giao diện nếu đang ở trang account (không bắt buộc nhưng tốt cho UX)
        updateUIForLoggedInUser();
    }

    // Chuẩn bị payload gửi đi (Thêm field address)
    const orderPayload = {
        email: currentUser.email,
        address: shippingAddress, // <-- QUAN TRỌNG: Gửi địa chỉ lên Backend
        cart: cart.map(item => ({
            product_code: String(item.product_code || item.id),
            quantity: Number(item.quantity)
        }))
    };

    const btn = document.querySelector('button[onclick="checkout()"]');
    if (btn) {
        btn.innerText = "Đang xử lý...";
        btn.disabled = true;
    }

    try {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
        if (currentUser.token) {
            headers['Authorization'] = 'Bearer ' + currentUser.token;
        }

        const response = await fetch(`${API_URL}/orders`, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(orderPayload)
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Lỗi không xác định từ server');
        }

        // Thành công
        alert('🎉 ' + result.message);
        cart = [];
        saveCart();
        updateCartDisplay();
        updateCartBadge();

        // Chuyển hướng
        const isIndex = window.location.pathname.endsWith('index.html') || window.location.pathname === '/' || window.location.pathname.endsWith('/');
        if (!isIndex) {
            window.location.href = '../pages/orders.html';
        } else {
            window.location.href = './pages/orders.html';
        }

    } catch (error) {
        console.error('Checkout error:', error);
        alert('❌ Đặt hàng thất bại: ' + error.message);
    } finally {
        if (btn) {
            btn.innerText = "Thanh toán (COD)";
            btn.disabled = false;
        }
    }
}



// Hàm này được gọi khi bấm nút "Tìm kiếm"
function triggerSearch() {
    const input = document.getElementById('searchInput');
    if (input) {
        const query = input.value;
        if(query.trim() === "") {
            alert("Vui lòng nhập từ khóa để tìm kiếm!");
            return;
        }
        searchProducts(query); // Gọi lại hàm search có sẵn của bạn
    }
}

// Bổ sung: Bắt sự kiện phím Enter ngay trong ô input (để chắc chắn)
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        // Tìm kiếm khi gõ (Real-time) - Code cũ của bạn đã có, giữ nguyên
        searchInput.addEventListener('input', (e) => {
            searchProducts(e.target.value);
        });

        // Tìm kiếm khi nhấn Enter
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault(); // Chặn reload trang
                triggerSearch();
            }
        });
    }
});