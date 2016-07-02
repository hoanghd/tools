package com.shinway.util;

import java.util.List;
import javax.inject.Inject;
import org.apache.ibatis.session.SqlSession;

public class Database {

    /** @Inject */
    @Inject
    private SqlSession sqlSession;

    public Database() {
    }

    /**
     * getSqlSession.
     * @return SqlSession
     */
    private SqlSession getSqlSession() {
        return this.sqlSession;
    }

    /**
     * deleteOne.
     * @param id String
     * @param parameterObject Object
     */
    public int deleteOne(String id, Object parameterObject) {
        final int rows = getSqlSession().delete(id, parameterObject);        
        return rows;
    }

    /**
     * insertOne.
     * @param id String
     * @param parameterObject Object
     */
    public int insertOne(String id, Object parameterObject) {
        final int rows = getSqlSession().insert(id, parameterObject);        
        return rows;
    }

    /**
     * selectOne
     * @param <T> Object
     * @param id String
     * @param parameterObject Object
     * @return <T>
     */
    @SuppressWarnings("unchecked")
    public <T> T selectOne(String id) {
        return (T) getSqlSession().selectOne(id);
    }
    
    @SuppressWarnings("unchecked")
    public <T> T selectOne(String id, Object parameterObject) {
        return (T) getSqlSession().selectOne(id, parameterObject);
    }

    /**
     * selectMany
     * @param <T> Object
     * @param id String
     * @param parameterObject Object
     * @param resultClass Class<T>
     * @return <T> Object
     */
    public <T> List<T> selectMany(String id, Object parameterObject, Class<T> resultClass) {
        @SuppressWarnings("unchecked")
        List<T> list = (List<T>) getSqlSession().selectList(id, parameterObject);
        checkResultClass(list, resultClass);
        return list;

    }

    /**
     * updateOne
     * @param id String
     * @param parameterObject Object
     */
    public int updateOne(String id, Object parameterObject) {
        final int rows = getSqlSession().update(id, parameterObject);
        return rows;
    }
    
    private static void checkResultClass(List<?> list, Class<?> resultClass) {
        if (list.isEmpty()) {
            return;
        }
        
        Object elem = list.get(0);
        Class<?> elemClass = elem.getClass();
        if (resultClass.equals(elemClass)) {
            return;
        }
    }
}
