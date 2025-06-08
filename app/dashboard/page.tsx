import { Header } from "@/components/header"
import { Footer } from "@/components/footer"
import { UserDashboard } from "@/components/user-dashboard"

export default function DashboardPage() {
  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <main className="container mx-auto py-16 px-4">
        <UserDashboard />
      </main>
      <Footer />
    </div>
  )
}
