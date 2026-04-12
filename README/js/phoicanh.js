document.addEventListener("DOMContentLoaded", function () {
  // Slider functionality
  let currentIndex = 0;
  const slides = document.querySelectorAll(".slide");
  const prevBtn = document.querySelector(".prev");
  const nextBtn = document.querySelector(".next");
  const dots = document.querySelectorAll(".dot");

  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.remove("active");
      if (dots[i]) dots[i].classList.remove("active");
      if (i === index) {
        slide.classList.add("active");
        if (dots[i]) dots[i].classList.add("active");
      }
    });
  }

  function nextSlide() {
    currentIndex = (currentIndex + 1) % slides.length;
    showSlide(currentIndex);
  }

  function prevSlide() {
    currentIndex = (currentIndex - 1 + slides.length) % slides.length;
    showSlide(currentIndex);
  }

  if (prevBtn && nextBtn) {
    prevBtn.addEventListener("click", prevSlide);
    nextBtn.addEventListener("click", nextSlide);
  }

  dots.forEach((dot, index) => {
    dot.addEventListener("click", () => {
      currentIndex = index;
      showSlide(currentIndex);
    });
  });

  // Auto slide
  setInterval(nextSlide, 5000);
  showSlide(currentIndex);

  // Modal handlers
  const modal = document.querySelector('#product-modal');
  const closeBtn = document.querySelector('.close');

  if (closeBtn) {
    closeBtn.addEventListener('click', function() {
      modal.style.display = 'none';
    });
  }

  if (modal) {
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });
  }

  // Click handlers for phoicanh items (hiển thị sản phẩm)
  const phoicanhItems = document.querySelectorAll('.phoicanh-item');
  phoicanhItems.forEach(item => {
    item.addEventListener('click', function() {
      showRandomProduct();
    });
  });

  // Hiển thị sản phẩm ngẫu nhiên
  function showRandomProduct() {
    $.ajax({
      url: 'phoicanh_api.php',
      method: 'POST',
      data: { action: 'get_sanpham_random' },
      dataType: 'json',
      success: function(response) {
        if (response.success && response.data.length > 0) {
          const randomProduct = response.data[Math.floor(Math.random() * response.data.length)];
          showProductDetail(randomProduct.sanpham_id);
        }
      },
      error: function() {
        console.error('Lỗi khi load sản phẩm');
      }
    });
  }

  // Hiển thị chi tiết sản phẩm
  function showProductDetail(sanphamId) {
    $.ajax({
      url: 'phoicanh_api.php',
      method: 'POST',
      data: { 
        action: 'get_sanpham',
        sanpham_id: sanphamId 
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          displayProductDetail(response.data);
          modal.style.display = 'block';
        }
      },
      error: function() {
        console.error('Lỗi khi load chi tiết sản phẩm');
      }
    });
  }

  // Hiển thị chi tiết sản phẩm trong modal
  function displayProductDetail(product) {
    const price = product.gia ? formatPrice(product.gia) + ' VNĐ' : 'Liên hệ';
    const productDetail = document.querySelector('#product-detail');
    
    productDetail.innerHTML = `
      <div class="product-detail">
        <div class="product-image">
          <img src="../foldercss/Anhsp/${getImagePath(product.chungloai_id)}/${product.image}" alt="${product.ten_sanpham}">
        </div>
        <div class="product-info">
          <h2>${product.ten_sanpham}</h2>
          <div class="info-group">
            <label>Mã sản phẩm:</label>
            <span>${product.ma_sp}</span>
          </div>
          <div class="info-group">
            <label>Bề mặt:</label>
            <span>${product.bemat}</span>
          </div>
          <div class="info-group">
            <label>Chất liệu:</label>
            <span>${product.chatlieu}</span>
          </div>
          <div class="info-group">
            <label>Công năng:</label>
            <span>${product.congnang}</span>
          </div>
          <div class="info-group">
            <label>Loại:</label>
            <span>${product.ten_loai || 'Không xác định'}</span>
          </div>
          <div class="info-group">
            <label>Kích thước:</label>
            <span>${product.ten_chungloai || 'Không xác định'}</span>
          </div>
          <div class="price">${price}</div>
        </div>
      </div>
    `;
  }

  // Lấy đường dẫn hình ảnh
  function getImagePath(chungloaiId) {
    switch(parseInt(chungloaiId)) {
      case 1:
        return 'cloai3030/Latnen';
      case 2:
        return 'cloai6060/Latnen';
      case 3:
        return 'cloai8080/Latnen';
      default:
        return 'cloai6060/Latnen';
    }
  }

  // Format giá tiền
  function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }
});

  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.remove("active");
      if (dots[i]) dots[i].classList.remove("active");
      if (i === index) {
        slide.classList.add("active");
        if (dots[i]) dots[i].classList.add("active");
      }
    });
  }

  function nextSlide() {
    currentIndex = (currentIndex + 1) % slides.length;
    showSlide(currentIndex);
  }

  function prevSlide() {
    currentIndex = (currentIndex - 1 + slides.length) % slides.length;
    showSlide(currentIndex);
  }

  if (prevBtn && nextBtn) {
    prevBtn.addEventListener("click", prevSlide);
    nextBtn.addEventListener("click", nextSlide);
  }

  dots.forEach((dot, index) => {
    dot.addEventListener("click", () => {
      currentIndex = index;
      showSlide(currentIndex);
    });
  });

  // Auto slide
  setInterval(nextSlide, 5000);
  showSlide(currentIndex);

  // Category click handlers
  const categoryItems = document.querySelectorAll(".category-item");
  const categoryLinks = document.querySelector(".category-links");
  const phoicanhDetail = document.querySelector(".phoicanh-detail");
  const backBtn = document.querySelector("#back-btn");
  const modal = document.querySelector("#product-modal");
  const closeBtn = document.querySelector(".close");

  categoryItems.forEach((item) => {
    item.addEventListener("click", function () {
      const category = this.dataset.category;
      showPhoicanhDetail(category);
    });
  });

  // Back button handler
  if (backBtn) {
    backBtn.addEventListener("click", function () {
      hidePhoicanhDetail();
    });
  }

  // Modal handlers
  if (closeBtn) {
    closeBtn.addEventListener("click", function () {
      modal.style.display = "none";
    });
  }

  if (modal) {
    modal.addEventListener("click", function (e) {
      if (e.target === modal) {
        modal.style.display = "none";
      }
    });
  }

  // Show phoi canh detail with slide effect
  function showPhoicanhDetail(category) {
    // Add slide left animation to category links
    categoryLinks.classList.add("slide-left");

    // Show detail section after animation
    setTimeout(() => {
      phoicanhDetail.style.display = "block";
      setTimeout(() => {
        phoicanhDetail.classList.add("show");
        loadPhoicanhData(category);
      }, 50);
    }, 500);
  }

  // Hide phoi canh detail
  function hidePhoicanhDetail() {
    phoicanhDetail.classList.remove("show");
    setTimeout(() => {
      phoicanhDetail.style.display = "none";
      categoryLinks.classList.remove("slide-left");
    }, 500);
  }

  // Load phoi canh data from database
  function loadPhoicanhData(category) {
    $.ajax({
      url: "phoicanh_api.php",
      method: "POST",
      data: { action: "get_phoicanh" },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          displayPhoicanhData(response.data);
        } else {
          console.error("Error loading phoi canh data");
        }
      },
      error: function () {
        console.error("AJAX error");
      },
    });
  }

  // Display phoi canh data
  function displayPhoicanhData(data) {
    const gallery = document.querySelector("#phoicanh-gallery");
    gallery.innerHTML = "";

    data.forEach((item) => {
      const div = document.createElement("div");
      div.className = "phoicanh-item";
      div.innerHTML = `
        <img src="../img/imgphoicanh/${item.hinhanh}" alt="Phối cảnh ${item.id_phoicanh}">
        <div class="info">
          <h4>Phối cảnh ${item.id_phoicanh}</h4>
          <p>${item.mota}</p>
        </div>
      `;

      // Add click handler to show product detail
      div.addEventListener("click", function () {
        showRandomProduct();
      });

      gallery.appendChild(div);
    });
  }

  // Show random product when clicking on phoi canh item
  function showRandomProduct() {
    $.ajax({
      url: "phoicanh_api.php",
      method: "POST",
      data: { action: "get_sanpham_random" },
      dataType: "json",
      success: function (response) {
        if (response.success && response.data.length > 0) {
          // Get random product from the list
          const randomProduct =
            response.data[Math.floor(Math.random() * response.data.length)];
          showProductDetail(randomProduct.sanpham_id);
        } else {
          console.error("Error loading products");
        }
      },
      error: function () {
        console.error("AJAX error loading products");
      },
    });
  }

  // Show product detail in modal
  function showProductDetail(sanphamId) {
    $.ajax({
      url: "phoicanh_api.php",
      method: "POST",
      data: {
        action: "get_sanpham",
        sanpham_id: sanphamId,
      },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          displayProductDetail(response.data);
          modal.style.display = "block";
        } else {
          console.error("Error loading product detail");
        }
      },
      error: function () {
        console.error("AJAX error loading product detail");
      },
    });
  }

  // Display product detail in modal
  function displayProductDetail(product) {
    const productDetail = document.querySelector("#product-detail");
    const price = product.gia ? formatPrice(product.gia) + " VNĐ" : "Liên hệ";

    productDetail.innerHTML = `
      <div class="product-detail">
        <div class="product-image">
          <img src="../foldercss/Anhsp/${getImagePath(product.chungloai_id)}/${
      product.image
    }" alt="${product.ten_sanpham}">
        </div>
        <div class="product-info">
          <h2>${product.ten_sanpham}</h2>
          <div class="info-group">
            <label>Mã sản phẩm:</label>
            <span>${product.ma_sp}</span>
          </div>
          <div class="info-group">
            <label>Bề mặt:</label>
            <span>${product.bemat}</span>
          </div>
          <div class="info-group">
            <label>Chất liệu:</label>
            <span>${product.chatlieu}</span>
          </div>
          <div class="info-group">
            <label>Công năng:</label>
            <span>${product.congnang}</span>
          </div>
          <div class="info-group">
            <label>Loại:</label>
            <span>${product.ten_loai || "Không xác định"}</span>
          </div>
          <div class="info-group">
            <label>Chủng loại:</label>
            <span>${product.ten_chungloai || "Không xác định"}</span>
          </div>
          <div class="price">${price}</div>
          <div class="cart-actions">
            <div class="quantity-control">
              <button type="button" class="qty-btn qty-minus">-</button>
              <input type="number" id="product-quantity" value="1" min="1" max="99">
              <button type="button" class="qty-btn qty-plus">+</button>
            </div>
            <button type="button" class="btn btn-primary" onclick="addToCart(${product.sanpham_id})">
              <i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng
            </button>
            <a href="sanpham.php?id=${product.sanpham_id}" class="btn btn-secondary">
              <i class="fas fa-eye"></i> Xem chi tiết
            </a>
          </div>
        </div>
      </div>
    `;

    // Add quantity control handlers
    const qtyMinus = productDetail.querySelector('.qty-minus');
    const qtyPlus = productDetail.querySelector('.qty-plus');
    const qtyInput = productDetail.querySelector('#product-quantity');

    qtyMinus.addEventListener('click', function() {
      const currentValue = parseInt(qtyInput.value);
      if (currentValue > 1) {
        qtyInput.value = currentValue - 1;
      }
    });

    qtyPlus.addEventListener('click', function() {
      const currentValue = parseInt(qtyInput.value);
      if (currentValue < 99) {
        qtyInput.value = currentValue + 1;
      }
    });
  }

  // Get image path based on chungloai_id
  function getImagePath(chungloaiId) {
    switch (chungloaiId) {
      case 1:
        return "cloai3030/Latnen";
      case 2:
        return "cloai6060/Latnen";
      case 3:
        return "cloai8080/Latnen";
      default:
        return "cloai6060/Latnen";
    }
  }

  // Format price
  function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }
});

// Global functions for cart
function addToCart(sanphamId) {
  const quantity = parseInt(document.getElementById('product-quantity').value) || 1;
  
  $.ajax({
    url: 'cart_api.php',
    method: 'POST',
    data: {
      action: 'add_to_cart',
      sanpham_id: sanphamId,
      quantity: quantity
    },
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        alert('Đã thêm sản phẩm vào giỏ hàng!');
        // Update cart count in header
        if (typeof updateCartCount === 'function') {
          updateCartCount();
        }
        // Close modal
        document.getElementById('product-modal').style.display = 'none';
      } else {
        alert(response.message || 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng');
      }
    },
    error: function() {
      alert('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng');
    }
  });
}
