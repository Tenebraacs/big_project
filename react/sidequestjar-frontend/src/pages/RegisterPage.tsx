import { useState } from 'react'
import Layout from '../components/Layout'
import { register } from '../services/api'

export default function RegisterPage() {
  const [form, setForm] = useState<any>({})
  const [errors, setErrors] = useState<any>({})
  const [message, setMessage] = useState<string>('')

  function handleChange(e: React.ChangeEvent<HTMLInputElement>) {
    setForm({
      ...form,
      [e.target.name]: e.target.value,
    })
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault()

    const data = await register(form)

    if (data.errors) {
      setErrors(data.errors)
      return
    }

    localStorage.setItem('token', data.token)
    setMessage('Registered!')
  }

  return (
    <Layout>
      <h1>Register</h1>

      <form onSubmit={handleSubmit}>
        <input name="username" placeholder="Username" onChange={handleChange} />
        {errors.username && <p>{errors.username[0]}</p>}

        <input name="email" placeholder="Email" onChange={handleChange} />
        {errors.email && <p>{errors.email[0]}</p>}

        <input type="password" name="password" placeholder="Password" onChange={handleChange} />
        {errors.password && <p>{errors.password[0]}</p>}

        <input type="password" name="password_confirmation" placeholder="Confirm Password" onChange={handleChange} />

        <button type="submit">Register</button>
      </form>

      {message && <p>{message}</p>}
    </Layout>
  )
}