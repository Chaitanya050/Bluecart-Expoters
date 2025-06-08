import { Header } from "@/components/header"
import { Footer } from "@/components/footer"
import { Card, CardContent } from "@/components/ui/card"
import { Users, Globe, Award, Truck } from "lucide-react"

export default function AboutPage() {
  return (
    <div className="min-h-screen bg-white">
      <Header />
      <main className="container mx-auto py-16 px-4">
        <div className="max-w-4xl mx-auto">
          <h1 className="text-4xl font-bold text-center mb-8 text-gray-800">About Bluecart Exporters</h1>

          <div className="prose prose-lg mx-auto mb-12">
            <p className="text-lg text-gray-600 text-center mb-8">
              Established in 2010, Bluecart Exporters has been a trusted name in the export industry, specializing in
              premium quality agricultural products and food items.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <Card className="text-center">
              <CardContent className="p-6">
                <Users className="h-12 w-12 text-blue-600 mx-auto mb-4" />
                <h3 className="font-semibold text-lg mb-2">500+ Clients</h3>
                <p className="text-gray-600">Trusted by businesses worldwide</p>
              </CardContent>
            </Card>

            <Card className="text-center">
              <CardContent className="p-6">
                <Globe className="h-12 w-12 text-blue-600 mx-auto mb-4" />
                <h3 className="font-semibold text-lg mb-2">25+ Countries</h3>
                <p className="text-gray-600">Global export network</p>
              </CardContent>
            </Card>

            <Card className="text-center">
              <CardContent className="p-6">
                <Award className="h-12 w-12 text-blue-600 mx-auto mb-4" />
                <h3 className="font-semibold text-lg mb-2">ISO Certified</h3>
                <p className="text-gray-600">Quality assurance guaranteed</p>
              </CardContent>
            </Card>

            <Card className="text-center">
              <CardContent className="p-6">
                <Truck className="h-12 w-12 text-blue-600 mx-auto mb-4" />
                <h3 className="font-semibold text-lg mb-2">Fast Delivery</h3>
                <p className="text-gray-600">Efficient logistics network</p>
              </CardContent>
            </Card>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div>
              <h2 className="text-2xl font-bold mb-4 text-gray-800">Our Mission</h2>
              <p className="text-gray-600 mb-6">
                To provide the highest quality agricultural products to global markets while maintaining sustainable
                farming practices and supporting local communities.
              </p>

              <h2 className="text-2xl font-bold mb-4 text-gray-800">Our Vision</h2>
              <p className="text-gray-600">
                To become the leading exporter of premium agricultural products, known for quality, reliability, and
                customer satisfaction across international markets.
              </p>
            </div>

            <div>
              <h2 className="text-2xl font-bold mb-4 text-gray-800">Why Choose Us?</h2>
              <ul className="space-y-3 text-gray-600">
                <li className="flex items-start">
                  <span className="text-blue-600 mr-2">•</span>
                  Premium quality products sourced directly from farmers
                </li>
                <li className="flex items-start">
                  <span className="text-blue-600 mr-2">•</span>
                  Rigorous quality control and testing procedures
                </li>
                <li className="flex items-start">
                  <span className="text-blue-600 mr-2">•</span>
                  Competitive pricing and flexible payment terms
                </li>
                <li className="flex items-start">
                  <span className="text-blue-600 mr-2">•</span>
                  Reliable shipping and logistics support
                </li>
                <li className="flex items-start">
                  <span className="text-blue-600 mr-2">•</span>
                  24/7 customer support and service
                </li>
              </ul>
            </div>
          </div>
        </div>
      </main>
      <Footer />
    </div>
  )
}
