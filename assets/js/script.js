// Add to cart functionality
function addToCart(productId) {
  const button = event.target
  const originalText = button.innerHTML

  // Show loading state
  button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...'
  button.disabled = true

  fetch("actions/add_to_cart.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "product_id=" + productId,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Update cart count in navbar
        updateCartCount()

        // Show success message
        showAlert("Product added to cart successfully!", "success")

        // Reset button
        button.innerHTML = '<i class="fas fa-check"></i> Added!'
        button.classList.remove("btn-success")
        button.classList.add("btn-outline-success")

        setTimeout(() => {
          button.innerHTML = originalText
          button.classList.remove("btn-outline-success")
          button.classList.add("btn-success")
          button.disabled = false
        }, 2000)
      } else {
        showAlert(data.message || "Error adding product to cart", "danger")
        button.innerHTML = originalText
        button.disabled = false
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      showAlert("Error adding product to cart", "danger")
      button.innerHTML = originalText
      button.disabled = false
    })
}

// Update cart count in navbar
function updateCartCount() {
  fetch("actions/get_cart_count.php")
    .then((response) => response.json())
    .then((data) => {
      const cartBadge = document.querySelector(".navbar .badge")
      const cartLink = document.querySelector('.navbar a[href="cart.php"]')

      if (data.count > 0) {
        if (cartBadge) {
          cartBadge.textContent = data.count
        } else {
          cartLink.innerHTML += `<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">${data.count}</span>`
        }
      }
    })
}

// Update quantity in cart
function updateQuantity(cartId, change) {
  const quantitySpan = document.querySelector(`#quantity-${cartId}`)
  const currentQuantity = Number.parseInt(quantitySpan.textContent)
  const newQuantity = currentQuantity + change

  if (newQuantity < 1) return

  fetch("actions/update_cart.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `cart_id=${cartId}&quantity=${newQuantity}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        quantitySpan.textContent = newQuantity

        // Update item total
        const price = Number.parseFloat(document.querySelector(`#price-${cartId}`).dataset.price)
        const itemTotal = document.querySelector(`#total-${cartId}`)
        itemTotal.textContent = "₹" + (price * newQuantity).toFixed(2)

        // Update cart totals
        updateCartTotals()
      }
    })
}

// Remove item from cart
function removeFromCart(cartId) {
  if (confirm("Are you sure you want to remove this item from your cart?")) {
    fetch("actions/remove_from_cart.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "cart_id=" + cartId,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          document.querySelector(`#cart-item-${cartId}`).remove()
          updateCartTotals()
          updateCartCount()
          showAlert("Item removed from cart", "success")

          // Check if cart is empty
          const cartItems = document.querySelectorAll(".cart-item")
          if (cartItems.length === 0) {
            location.reload()
          }
        }
      })
  }
}

// Update cart totals
function updateCartTotals() {
  let subtotal = 0
  document.querySelectorAll('[id^="total-"]').forEach((element) => {
    const amount = Number.parseFloat(element.textContent.replace("₹", ""))
    subtotal += amount
  })

  const tax = subtotal * 0.18
  const total = subtotal + tax

  document.querySelector("#subtotal").textContent = "₹" + subtotal.toFixed(2)
  document.querySelector("#tax").textContent = "₹" + tax.toFixed(2)
  document.querySelector("#total").textContent = "₹" + total.toFixed(2)
}

// Show alert messages
function showAlert(message, type) {
  const alertDiv = document.createElement("div")
  alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`
  alertDiv.style.cssText = "top: 20px; right: 20px; z-index: 9999; min-width: 300px;"
  alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `

  document.body.appendChild(alertDiv)

  setTimeout(() => {
    alertDiv.remove()
  }, 5000)
}

// Form validation
function validateForm(formId) {
  const form = document.getElementById(formId)
  const inputs = form.querySelectorAll("input[required], textarea[required]")
  let isValid = true

  inputs.forEach((input) => {
    if (!input.value.trim()) {
      input.classList.add("is-invalid")
      isValid = false
    } else {
      input.classList.remove("is-invalid")
    }
  })

  return isValid
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault()
    const target = document.querySelector(this.getAttribute("href"))
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      })
    }
  })
})

// Add fade-in animation to elements when they come into view
const observerOptions = {
  threshold: 0.1,
  rootMargin: "0px 0px -50px 0px",
}

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add("fade-in")
    }
  })
}, observerOptions)

document.querySelectorAll(".card, .feature-card, .product-card").forEach((el) => {
  observer.observe(el)
})
