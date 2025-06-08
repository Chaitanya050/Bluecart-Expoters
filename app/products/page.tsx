import { Header } from "@/components/header"
import { Footer } from "@/components/footer"
import { ProductGrid } from "@/components/product-grid"
import { ProductFilters } from "@/components/product-filters"

export default function ProductsPage() {
  return (
    <div className="min-h-screen bg-white">
      <Header />
      <main className="container mx-auto py-16 px-4">
        <h1 className="text-4xl font-bold text-center mb-12 text-gray-800">Our Products</h1>

        <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
          <div className="lg:col-span-1">
            <ProductFilters />
          </div>
          <div className="lg:col-span-3">
            <ProductGrid />
          </div>
        </div>
      </main>
      <Footer />
    </div>
  )
}
