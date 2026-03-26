import Navbar from './Navbar'

type Props = {
  children: React.ReactNode
}

export default function Layout({ children }: Props) {
  return (
    <div>
      <Navbar />
      <main className="py-4">{children}</main>
    </div>
  )
}