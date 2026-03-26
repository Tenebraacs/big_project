export default function Navbar() {
  const token = localStorage.getItem('token')

  function logout() {
    localStorage.removeItem('token')
    window.location.href = '/login'
  }

  return (
    <nav style={{ padding: '1rem', borderBottom: '1px solid #ccc' }}>
      <a href="/">SideQuestJar</a>

      {!token ? (
        <>
          <a href="/login" style={{ marginLeft: '1rem' }}>Login</a>
          <a href="/register" style={{ marginLeft: '1rem' }}>Register</a>
        </>
      ) : (
        <button onClick={logout} style={{ marginLeft: '1rem' }}>
          Logout
        </button>
      )}
    </nav>
  )
}