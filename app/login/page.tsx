import { LoginForm } from "@/components/auth/login-form"
import { Header } from "@/components/header"
import { Footer } from "@/components/footer"

export default function LoginPage() {
  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <main className="container mx-auto py-16 px-4">
        <div className="max-w-md mx-auto">
          <h2 className="text-3xl font-bold text-center mb-8 text-gray-800">Login to Your Account</h2>
          <LoginForm />
        </div>
      </main>
      <Footer />
    </div>
  )
}
