"use client"

import type React from "react"

interface Product {
  id: number
  name: string
  price: number
  image_url: string
  stock_quantity: number
  low_stock_threshold: number
  description: string
}

interface ProductCardProps {
  product: Product
  addToCart: (product: Product) => void
}

const ProductCard: React.FC<ProductCardProps> = ({ product, addToCart }) => {
  const handleAddToCart = () => {
    addToCart(product)
  }

  return (
    <div className="card">
      <img src={product.image_url || "/placeholder.svg"} className="card-img-top" alt={product.name} />
      <div className="card-body">
        <h5 className="card-title">{product.name}</h5>
        <p className="card-text">Price: ${product.price}</p>

        {/* Stock Status */}
        <div className="mt-2">
          {product.stock_quantity === 0 ? (
            <span className="badge bg-danger">Out of Stock</span>
          ) : product.stock_quantity <= product.low_stock_threshold ? (
            <span className="badge bg-warning">Low Stock ({product.stock_quantity} left)</span>
          ) : (
            <span className="badge bg-success">In Stock</span>
          )}
        </div>

        <Button onClick={() => addToCart(product)} className="w-full" disabled={product.stock_quantity === 0}>
          {product.stock_quantity === 0 ? "Out of Stock" : "Add to Cart"}
        </Button>
      </div>
    </div>
  )
}

const Button = ({
  onClick,
  className,
  children,
  disabled,
}: { onClick: () => void; className: string; children: React.ReactNode; disabled?: boolean }) => {
  return (
    <button onClick={onClick} className={`btn btn-primary ${className}`} disabled={disabled}>
      {children}
    </button>
  )
}

// Export as both named and default export to support both import styles
export { ProductCard }
export default ProductCard
