"use client"

import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Checkbox } from "@/components/ui/checkbox"
import { Label } from "@/components/ui/label"
import { Slider } from "@/components/ui/slider"

export function ProductFilters() {
  const categories = ["Rice & Grains", "Spices & Herbs", "Lentils & Pulses", "Tea & Beverages", "Oils & Condiments"]

  return (
    <div className="space-y-6">
      <Card>
        <CardHeader>
          <CardTitle>Categories</CardTitle>
        </CardHeader>
        <CardContent className="space-y-3">
          {categories.map((category) => (
            <div key={category} className="flex items-center space-x-2">
              <Checkbox id={category} />
              <Label htmlFor={category} className="text-sm">
                {category}
              </Label>
            </div>
          ))}
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Price Range</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            <Slider defaultValue={[0, 500]} max={500} step={10} className="w-full" />
            <div className="flex justify-between text-sm text-gray-600">
              <span>₹0</span>
              <span>₹500</span>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
