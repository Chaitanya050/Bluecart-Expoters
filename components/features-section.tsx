import { Card, CardContent } from "@/components/ui/card"
import { Shield, Truck, Globe, Award } from "lucide-react"

export function FeaturesSection() {
  const features = [
    {
      icon: Shield,
      title: "Quality Assured",
      description: "All products undergo rigorous quality control and testing procedures",
    },
    {
      icon: Truck,
      title: "Fast Shipping",
      description: "Efficient logistics network ensuring timely delivery worldwide",
    },
    {
      icon: Globe,
      title: "Global Reach",
      description: "Serving customers in over 25 countries across the globe",
    },
    {
      icon: Award,
      title: "ISO Certified",
      description: "Certified quality management systems and processes",
    },
  ]

  return (
    <section className="py-16 bg-gray-50">
      <div className="container mx-auto px-4">
        <h2 className="text-3xl font-bold text-center mb-12 text-gray-800">Why Choose Bluecart Exporters?</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {features.map((feature, index) => (
            <Card key={index} className="text-center hover:shadow-lg transition-shadow">
              <CardContent className="p-6">
                <feature.icon className="h-12 w-12 text-blue-600 mx-auto mb-4" />
                <h3 className="text-xl font-semibold mb-3 text-gray-800">{feature.title}</h3>
                <p className="text-gray-600">{feature.description}</p>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    </section>
  )
}
