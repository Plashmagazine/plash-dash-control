import { useState, useEffect, createContext, useContext } from 'react';
import { User, UserRole } from '@/types';

interface AuthContextType {
  user: User | null;
  isAuthenticated: boolean;
  login: (email: string, password: string) => Promise<{ success: boolean; error?: string }>;
  logout: () => void;
  loading: boolean;
}

const AuthContext = createContext<AuthContextType>({
  user: null,
  isAuthenticated: false,
  login: async () => ({ success: false }),
  logout: () => {},
  loading: true,
});

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

export const useAuthProvider = () => {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Verificar se há um usuário logado no localStorage
    const checkAuth = () => {
      try {
        const savedUser = localStorage.getItem('plash_user');
        const savedToken = localStorage.getItem('plash_token');
        
        if (savedUser && savedToken) {
          setUser(JSON.parse(savedUser));
        }
      } catch (error) {
        console.error('Erro ao verificar autenticação:', error);
        localStorage.removeItem('plash_user');
        localStorage.removeItem('plash_token');
      } finally {
        setLoading(false);
      }
    };

    checkAuth();
  }, []);

  const login = async (email: string, password: string): Promise<{ success: boolean; error?: string }> => {
    setLoading(true);
    
    try {
      // Simulação de autenticação para desenvolvimento
      // Em produção, isso seria uma chamada real para a API
      if (email === 'admin@plashmagazine.com' && password === 'admin123') {
        const adminUser: User = {
          id: '1',
          name: 'Administrador Plash',
          email: 'admin@plashmagazine.com',
          role: 'admin',
          sub_role: 'ceo',
          status: 'ativo',
          badges: ['verificado'],
          created_at: new Date().toISOString(),
          updated_at: new Date().toISOString(),
          last_login: new Date().toISOString(),
        };

        setUser(adminUser);
        localStorage.setItem('plash_user', JSON.stringify(adminUser));
        localStorage.setItem('plash_token', 'demo_token_admin');
        
        return { success: true };
      }

      // Outros usuários de demonstração
      const demoUsers: Record<string, User> = {
        'colaborador@plash.com': {
          id: '2',
          name: 'João Colaborador',
          email: 'colaborador@plash.com',
          role: 'collaborator',
          status: 'ativo',
          badges: ['verificado', 'responsavel_proativo'],
          created_at: new Date().toISOString(),
          updated_at: new Date().toISOString(),
          last_login: new Date().toISOString(),
        },
        'atleta@plash.com': {
          id: '3',
          name: 'Maria Skatista',
          email: 'atleta@plash.com',
          role: 'athlete',
          status: 'ativo',
          badges: ['verificado'],
          created_at: new Date().toISOString(),
          updated_at: new Date().toISOString(),
          last_login: new Date().toISOString(),
        },
        'editora@plash.com': {
          id: '4',
          name: 'Editora Parceira',
          email: 'editora@plash.com',
          role: 'partner',
          status: 'ativo',
          badges: ['verificado', 'compromisso_editorial'],
          created_at: new Date().toISOString(),
          updated_at: new Date().toISOString(),
          last_login: new Date().toISOString(),
        },
      };

      if (demoUsers[email] && password === 'demo123') {
        const demoUser = demoUsers[email];
        setUser(demoUser);
        localStorage.setItem('plash_user', JSON.stringify(demoUser));
        localStorage.setItem('plash_token', `demo_token_${demoUser.role}`);
        
        return { success: true };
      }

      return { success: false, error: 'Email ou senha incorretos' };
    } catch (error) {
      console.error('Erro no login:', error);
      return { success: false, error: 'Erro interno do sistema' };
    } finally {
      setLoading(false);
    }
  };

  const logout = () => {
    setUser(null);
    localStorage.removeItem('plash_user');
    localStorage.removeItem('plash_token');
  };

  return {
    user,
    isAuthenticated: !!user,
    login,
    logout,
    loading,
  };
};

export { AuthContext };