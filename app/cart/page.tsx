import { Header } from "@/components/header"
import { Footer } from "@/components/footer"
import { ShoppingCart } from "@/components/shopping-cart"

export default function CartPage() {
  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <main className="container mx-auto py-16 px-4">
        <h2 className="text-4xl font-bold text-center mb-12 text-gray-800">Shopping Cart</h2>
        <ShoppingCart />
      </main>
      <Footer />
    </div>
  )
}
